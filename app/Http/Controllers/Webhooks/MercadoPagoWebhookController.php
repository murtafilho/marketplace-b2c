<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoWebhookController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }

    public function payment(Request $request)
    {
        try {
            // Log webhook data for debugging
            Log::info('MercadoPago Webhook Payment received:', $request->all());

            // Validate webhook secret if configured
            $webhookSecret = config('mercadopago.webhook_secret');
            if ($webhookSecret && $webhookSecret !== 'xxxxx') {
                $signature = $request->header('X-Signature');
                if (!$this->validateSignature($request->getContent(), $signature, $webhookSecret)) {
                    Log::warning('Invalid webhook signature');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            // Process webhook
            $processed = $this->mercadoPagoService->processWebhook($request->all());

            if ($processed) {
                Log::info('Webhook processed successfully');
                return response()->json(['status' => 'success'], 200);
            } else {
                Log::warning('Failed to process webhook');
                return response()->json(['status' => 'error'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function merchantOrder(Request $request)
    {
        try {
            Log::info('MercadoPago Webhook Merchant Order received:', $request->all());
            
            // For now, just acknowledge the webhook
            // Implement merchant order processing if needed
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('Merchant order webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    private function validateSignature(string $content, ?string $signature, string $secret): bool
    {
        if (!$signature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $content, $secret);
        
        return hash_equals($expectedSignature, $signature);
    }
}