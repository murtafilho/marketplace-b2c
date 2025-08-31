<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $cart = $this->getCart();
        $cartItems = $cart ? $cart->items()->with(['product.images', 'productVariation'])->get() : collect();
        
        return view('shop.cart.index', compact('cart', 'cartItems'));
    }

    /**
     * Add item to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_variation_id' => 'nullable|exists:product_variations,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Verificar se produto está ativo
        if ($product->status !== 'active') {
            return back()->with('error', 'Este produto não está disponível para compra.');
        }
        
        // Check stock
        if ($product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Produto não tem estoque suficiente.');
        }

        $cart = $this->getOrCreateCart();
        
        // Check if item already exists
        $existingItem = $cart->items()
            ->where('product_id', $request->product_id)
            ->where('product_variation_id', $request->product_variation_id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;
            if ($product->stock_quantity < $newQuantity) {
                return back()->with('error', 'Não é possível adicionar mais itens. Estoque insuficiente.');
            }
            
            $existingItem->update([
                'quantity' => $newQuantity,
                'total_price' => $newQuantity * $existingItem->unit_price
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'product_variation_id' => $request->product_variation_id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'total_price' => $product->price * $request->quantity,
                'product_snapshot' => $product->toArray()
            ]);
        }

        $this->updateCartTotals($cart);

        // Se for "Comprar Agora", redirecionar para checkout
        if ($request->has('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'Produto adicionado ao carrinho!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check if item belongs to current cart
        $cart = $this->getCart();
        if (!$cart || $item->cart_id !== $cart->id) {
            return response()->json(['error' => 'Item não encontrado'], 404);
        }

        // Check stock
        if ($item->product->stock_quantity < $request->quantity) {
            return response()->json(['error' => 'Estoque insuficiente'], 400);
        }

        $item->update([
            'quantity' => $request->quantity,
            'total_price' => $request->quantity * $item->unit_price
        ]);

        $this->updateCartTotals($cart);

        return response()->json([
            'success' => true,
            'item_total' => $item->total_price,
            'cart_total' => $cart->total_amount
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(CartItem $item)
    {
        $cart = $this->getCart();
        if (!$cart || $item->cart_id !== $cart->id) {
            return response()->json(['error' => 'Item não encontrado'], 404);
        }

        $item->delete();
        $this->updateCartTotals($cart);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart_total' => $cart->total_amount,
                'cart_items' => $cart->total_items
            ]);
        }

        return back()->with('success', 'Item removido do carrinho!');
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->items()->delete();
            $cart->update([
                'total_amount' => 0,
                'total_items' => 0
            ]);
        }

        return back()->with('success', 'Carrinho limpo!');
    }

    /**
     * Get current cart for user/session.
     */
    private function getCart()
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->first();
        }
        
        return Cart::where('session_id', session()->getId())->first();
    }

    /**
     * Get or create cart for user/session.
     */
    private function getOrCreateCart()
    {
        $cart = $this->getCart();
        
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => auth()->id(),
                'session_id' => auth()->check() ? null : session()->getId(),
                'total_amount' => 0,
                'total_items' => 0
            ]);
        }

        return $cart;
    }

    /**
     * Update cart totals.
     */
    private function updateCartTotals(Cart $cart)
    {
        $items = $cart->items;
        
        $cart->update([
            'total_amount' => $items->sum('total_price'),
            'total_items' => $items->sum('quantity'),
            'last_activity' => now()
        ]);
    }
}
