<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SubOrder;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
    }
    /**
     * Show checkout page.
     */
    public function index()
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        $cartItems = $cart->items()->with(['product.seller.user', 'productVariation'])->get();
        $itemsBySeller = $cartItems->groupBy('product.seller_id');

        return view('shop.checkout.index', compact('cart', 'cartItems', 'itemsBySeller'));
    }

    /**
     * Process checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'billing_address' => 'required|array',
            'billing_address.name' => 'required|string',
            'billing_address.email' => 'required|email',
            'billing_address.phone' => 'required|string',
            'billing_address.address' => 'required|string',
            'billing_address.city' => 'required|string',
            'billing_address.state' => 'required|string',
            'billing_address.postal_code' => 'required|string',
            'payment_method' => 'required|in:pix,credit_card,boleto',
            'shipping_address' => 'nullable|array'
        ]);

        $cart = $this->getCart();
        
        if (!$cart || $cart->items->count() === 0) {
            return back()->with('error', 'Carrinho vazio.');
        }

        // Create main order
        $order = Order::create([
            'order_number' => $this->generateOrderNumber(),
            'user_id' => auth()->id(),
            'status' => 'pending',
            'subtotal' => $cart->total_amount,
            'shipping_total' => 0, // Calculate shipping later
            'tax_total' => 0,
            'discount_total' => 0,
            'total' => $cart->total_amount,
            'currency' => 'BRL',
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address ?? $request->billing_address
        ]);

        // Group items by seller and create sub-orders
        $cartItems = $cart->items()->with(['product.seller'])->get();
        $itemsBySeller = $cartItems->groupBy('product.seller_id');

        foreach ($itemsBySeller as $sellerId => $sellerItems) {
            $seller = $sellerItems->first()->product->seller;
            
            // Create sub-order
            $subOrder = SubOrder::create([
                'sub_order_number' => $this->generateSubOrderNumber($order->order_number, $sellerId),
                'order_id' => $order->id,
                'seller_id' => $sellerId,
                'status' => 'pending',
                'subtotal' => $sellerItems->sum('total_price'),
                'shipping_cost' => 0, // Calculate shipping later
                'commission_rate' => $seller->commission_rate,
                'commission_amount' => $sellerItems->sum('total_price') * ($seller->commission_rate / 100),
                'seller_amount' => $sellerItems->sum('total_price') * (1 - $seller->commission_rate / 100)
            ]);

            // Create order items
            foreach ($sellerItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'sub_order_id' => $subOrder->id,
                    'product_id' => $cartItem->product_id,
                    'product_variation_id' => $cartItem->product_variation_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->total_price,
                    'product_snapshot' => $cartItem->product_snapshot,
                    'variation_snapshot' => $cartItem->variation_snapshot,
                    'commission_rate' => $seller->commission_rate,
                    'commission_amount' => $cartItem->total_price * ($seller->commission_rate / 100),
                    'seller_amount' => $cartItem->total_price * (1 - $seller->commission_rate / 100)
                ]);

                // Update product stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }
        }

        // Clear cart
        $cart->items()->delete();
        $cart->update(['total_amount' => 0, 'total_items' => 0]);

        // Create MercadoPago payment preference
        $paymentResult = $this->mercadoPagoService->createPaymentPreference($order);
        
        if (!$paymentResult['success']) {
            $order->update(['status' => 'failed', 'payment_status' => 'failed']);
            return back()->with('error', 'Erro ao processar pagamento: ' . $paymentResult['error']);
        }

        // Store preference data
        $order->update([
            'payment_preference_id' => $paymentResult['preference_id'],
            'payment_data' => json_encode($paymentResult['data'])
        ]);

        // Redirect to MercadoPago checkout
        $checkoutUrl = config('app.env') === 'production' 
            ? $paymentResult['init_point']
            : $paymentResult['sandbox_init_point'];
            
        return redirect($checkoutUrl);
    }

    /**
     * Checkout success page.
     */
    public function success(Order $order)
    {
        // Verify order belongs to current user
        if ($order->user_id !== auth()->id()) {
            abort(404);
        }

        return view('shop.checkout.success', compact('order'));
    }

    /**
     * Checkout pending page.
     */
    public function pending()
    {
        return view('shop.checkout.pending');
    }

    /**
     * Checkout cancel page.
     */
    public function cancel()
    {
        return view('shop.checkout.cancel');
    }

    /**
     * Get current cart.
     */
    private function getCart()
    {
        return Cart::where('user_id', auth()->id())->first();
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD' . date('Ymd') . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Generate sub-order number.
     */
    private function generateSubOrderNumber($orderNumber, $sellerId)
    {
        return $orderNumber . '-S' . str_pad($sellerId, 3, '0', STR_PAD_LEFT);
    }
}
