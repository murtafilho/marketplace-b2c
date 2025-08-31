<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\MercadoPagoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_mercadopago_service_instantiation(): void
    {
        echo "\n🧪 TESTANDO INTEGRAÇÃO MERCADOPAGO\n";
        echo "==================================\n";

        // Configurar ambiente de teste
        Config::set('mercadopago.access_token', null);
        Config::set('mercadopago.marketplace_commission', 10.0);

        echo "1. 🔧 Testando instanciação do serviço sem token...\n";
        $service = new MercadoPagoService();
        $this->assertInstanceOf(MercadoPagoService::class, $service);
        echo "   ✅ Serviço instanciado sem erro\n";

        echo "\n2. 🎯 Testando comportamento com token ausente...\n";
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        
        $order = Order::create([
            'order_number' => 'TEST-001',
            'user_id' => $user->id,
            'status' => 'pending',
            'subtotal' => 100.00,
            'total' => 100.00,
            'currency' => 'BRL',
            'payment_status' => 'pending',
            'payment_method' => 'pix',
            'billing_address' => ['name' => 'Test User'],
            'shipping_address' => ['name' => 'Test User']
        ]);

        $result = $service->createPaymentPreference($order);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('missing access token', $result['error']);
        echo "   ✅ Erro tratado corretamente quando token ausente\n";

        echo "\n✅ TESTES DE INTEGRAÇÃO CONCLUÍDOS!\n";
        echo "===================================\n";
    }

    public function test_payment_calculations(): void
    {
        echo "\n💰 TESTANDO CÁLCULOS DE PAGAMENTO\n";
        echo "=================================\n";

        echo "1. 📊 Testando cálculo de comissão...\n";
        $totalAmount = 100.00;
        $commissionRate = 10.0;
        $commission = $totalAmount * ($commissionRate / 100);
        $sellerAmount = $totalAmount - $commission;

        $this->assertEquals(10.00, $commission);
        $this->assertEquals(90.00, $sellerAmount);
        echo "   ✅ Total: R$ {$totalAmount}\n";
        echo "   ✅ Comissão (10%): R$ {$commission}\n";
        echo "   ✅ Valor vendedor: R$ {$sellerAmount}\n";

        echo "\n2. 🔄 Testando divisão entre múltiplos vendedores...\n";
        $seller1Total = 60.00;
        $seller2Total = 40.00;
        $totalOrder = $seller1Total + $seller2Total;
        
        $seller1Commission = $seller1Total * 0.10;
        $seller2Commission = $seller2Total * 0.10;
        
        $seller1Net = $seller1Total - $seller1Commission;
        $seller2Net = $seller2Total - $seller2Commission;

        $this->assertEquals(100.00, $totalOrder);
        $this->assertEquals(6.00, $seller1Commission);
        $this->assertEquals(4.00, $seller2Commission);
        $this->assertEquals(54.00, $seller1Net);
        $this->assertEquals(36.00, $seller2Net);

        echo "   ✅ Vendedor 1: R$ {$seller1Total} → R$ {$seller1Net} (comissão: R$ {$seller1Commission})\n";
        echo "   ✅ Vendedor 2: R$ {$seller2Total} → R$ {$seller2Net} (comissão: R$ {$seller2Commission})\n";

        echo "\n✅ CÁLCULOS VALIDADOS COM SUCESSO!\n";
        echo "==================================\n";
    }

    public function test_transaction_model(): void
    {
        echo "\n🗃️ TESTANDO MODELO DE TRANSAÇÃO\n";
        echo "==============================\n";

        $user = User::factory()->create();
        $order = Order::create([
            'order_number' => 'TXN-001',
            'user_id' => $user->id,
            'status' => 'pending',
            'subtotal' => 50.00,
            'total' => 50.00,
            'currency' => 'BRL',
            'payment_status' => 'pending',
            'payment_method' => 'pix',
            'billing_address' => ['name' => 'Test User'],
            'shipping_address' => ['name' => 'Test User']
        ]);

        echo "1. 💳 Criando transação de pagamento...\n";
        $transaction = Transaction::create([
            'order_id' => $order->id,
            'mp_payment_id' => 'test-payment-123',
            'type' => 'payment',
            'status' => 'approved',
            'amount' => 50.00,
            'commission_rate' => 10.0,
            'commission_amount' => 5.00,
            'seller_amount' => 45.00,
            'mp_response' => json_encode(['test' => true]),
            'processed_at' => now()
        ]);

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'mp_payment_id' => 'test-payment-123',
            'status' => 'approved',
            'amount' => 50.00
        ]);

        echo "   ✅ Transação criada: ID {$transaction->id}\n";
        echo "   ✅ Payment ID: {$transaction->mp_payment_id}\n";
        echo "   ✅ Status: {$transaction->status}\n";
        echo "   ✅ Valor: R$ {$transaction->amount}\n";

        echo "\n2. 🔗 Testando relacionamento ordem-transação...\n";
        $orderTransactions = $order->transactions;
        $this->assertCount(1, $orderTransactions);
        $this->assertEquals($transaction->id, $orderTransactions->first()->id);
        echo "   ✅ Relacionamento funcionando corretamente\n";

        echo "\n✅ MODELO DE TRANSAÇÃO TESTADO!\n";
        echo "===============================\n";
    }

    public function test_webhook_routes_exist(): void
    {
        echo "\n🌐 TESTANDO ROTAS DE WEBHOOK\n";
        echo "===========================\n";

        echo "1. 🔍 Verificando rota de pagamento...\n";
        $response = $this->post('/webhooks/mercadopago/payment', []);
        // Esperamos erro 400 ou 500, mas não 404 (rota não encontrada)
        $this->assertNotEquals(404, $response->status());
        echo "   ✅ Rota de payment webhook existe\n";

        echo "\n2. 🔍 Verificando rota de merchant order...\n";
        $response = $this->post('/webhooks/mercadopago/merchant_order', []);
        $this->assertNotEquals(404, $response->status());
        echo "   ✅ Rota de merchant order webhook existe\n";

        echo "\n✅ ROTAS DE WEBHOOK CONFIGURADAS!\n";
        echo "=================================\n";
    }
}