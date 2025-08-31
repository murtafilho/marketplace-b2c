<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    protected $preferenceClient;
    protected $paymentClient;

    public function __construct()
    {
        $accessToken = config('mercadopago.access_token');
        if ($accessToken) {
            MercadoPagoConfig::setAccessToken($accessToken);
            $this->preferenceClient = new PreferenceClient();
            $this->paymentClient = new PaymentClient();
        }
    }

    public function createPaymentPreference(Order $order): array
    {
        if (!$this->preferenceClient) {
            return [
                'success' => false,
                'error' => 'MercadoPago not configured - missing access token'
            ];
        }

        try {
            $items = [];
            $marketplace_fee = 0;

            // Processar itens do pedido
            foreach ($order->items as $item) {
                $unitPrice = (float) $item->price;
                $quantity = (int) $item->quantity;
                $totalItemPrice = $unitPrice * $quantity;
                
                // Calcular comissão do marketplace para este item
                $itemCommission = $totalItemPrice * (config('mercadopago.marketplace_commission') / 100);
                $marketplace_fee += $itemCommission;

                $items[] = [
                    "id" => (string) $item->product_id,
                    "title" => $item->product_name,
                    "description" => $item->product->short_description ?? '',
                    "quantity" => $quantity,
                    "unit_price" => $unitPrice,
                    "currency_id" => "BRL"
                ];
            }

            // Dados da preferência
            $preferenceData = [
                "items" => $items,
                "marketplace_fee" => $marketplace_fee,
                "payer" => [
                    "name" => $order->user->name,
                    "email" => $order->user->email,
                    "phone" => [
                        "number" => $order->user->phone ?? ""
                    ]
                ],
                "back_urls" => [
                    "success" => route('checkout.success'),
                    "failure" => route('checkout.index'),
                    "pending" => route('checkout.pending')
                ],
                "auto_return" => "approved",
                "external_reference" => (string) $order->id,
                "notification_url" => config('app.url') . "/webhooks/mercadopago/payment",
                "expires" => true,
                "expiration_date_from" => now()->toISOString(),
                "expiration_date_to" => now()->addHours(24)->toISOString()
            ];

            // Split de pagamento para múltiplos vendedores
            if ($order->items->groupBy('seller_id')->count() > 1) {
                $preferenceData['additional_info'] = [
                    'marketplace' => [
                        'fee_percentage' => config('mercadopago.marketplace_commission')
                    ]
                ];
            }

            $preference = $this->preferenceClient->create($preferenceData);

            return [
                'success' => true,
                'preference_id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
                'data' => $preference
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago API Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getApiResponse()->getStatusCode()
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPago Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getPayment(string $paymentId): array
    {
        try {
            $payment = $this->paymentClient->get($paymentId);
            
            return [
                'success' => true,
                'payment' => $payment
            ];

        } catch (MPApiException $e) {
            Log::error('MercadoPago Get Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function processWebhook(array $data): bool
    {
        try {
            if (!isset($data['type']) || $data['type'] !== 'payment') {
                return false;
            }

            $paymentId = $data['data']['id'];
            $paymentResult = $this->getPayment($paymentId);

            if (!$paymentResult['success']) {
                return false;
            }

            $payment = $paymentResult['payment'];
            $orderId = $payment->external_reference;
            $order = Order::find($orderId);

            if (!$order) {
                Log::warning("Order not found for payment: {$paymentId}");
                return false;
            }

            // Atualizar ou criar transação
            $transaction = Transaction::updateOrCreate(
                ['payment_id' => $paymentId],
                [
                    'order_id' => $order->id,
                    'payment_method' => $payment->payment_method_id,
                    'status' => $payment->status,
                    'amount' => $payment->transaction_amount,
                    'currency' => $payment->currency_id,
                    'payment_data' => json_encode($payment),
                    'processed_at' => now()
                ]
            );

            // Atualizar status do pedido baseado no status do pagamento
            switch ($payment->status) {
                case 'approved':
                    $order->update(['status' => 'paid']);
                    $this->processPaidOrder($order, $transaction);
                    break;
                case 'pending':
                    $order->update(['status' => 'pending_payment']);
                    break;
                case 'rejected':
                case 'cancelled':
                    $order->update(['status' => 'cancelled']);
                    break;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return false;
        }
    }

    protected function processPaidOrder(Order $order, Transaction $transaction): void
    {
        // Calcular divisão dos valores para os vendedores
        $totalAmount = $transaction->amount;
        $marketplaceCommission = $totalAmount * (config('mercadopago.marketplace_commission') / 100);
        $sellersAmount = $totalAmount - $marketplaceCommission;

        // Agrupar itens por vendedor
        $itemsBySeller = $order->items->groupBy('seller_id');

        foreach ($itemsBySeller as $sellerId => $sellerItems) {
            $sellerTotal = $sellerItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $sellerPercentage = $sellerTotal / $order->total;
            $sellerAmount = $sellersAmount * $sellerPercentage;

            // Criar transação do vendedor
            Transaction::create([
                'order_id' => $order->id,
                'seller_id' => $sellerId,
                'type' => 'seller_payment',
                'amount' => $sellerAmount,
                'commission' => $sellerTotal * (config('mercadopago.marketplace_commission') / 100),
                'status' => 'approved',
                'payment_id' => $transaction->payment_id . '_seller_' . $sellerId,
                'processed_at' => now()
            ]);
        }

        // Criar transação da comissão do marketplace
        Transaction::create([
            'order_id' => $order->id,
            'type' => 'marketplace_commission',
            'amount' => $marketplaceCommission,
            'status' => 'approved',
            'payment_id' => $transaction->payment_id . '_commission',
            'processed_at' => now()
        ]);
    }
}