<?php
/**
 * Arquivo: tests/Feature/ShoppingCartTest.php
 * Descrição: Testes completos do carrinho de compras
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingCartTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $seller;
    protected $product;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar categoria
        $this->category = Category::create([
            'name' => 'Eletrônicos',
            'slug' => 'eletronicos',
            'is_active' => true
        ]);

        // Criar cliente
        $this->customer = User::factory()->create([
            'role' => 'customer'
        ]);

        // Criar vendedor
        $this->seller = User::factory()->create([
            'role' => 'seller'
        ]);

        // Criar perfil do vendedor
        SellerProfile::create([
            'user_id' => $this->seller->id,
            'document_type' => 'CPF',
            'document_number' => '12345678901',
            'status' => 'approved',
            'commission_rate' => 10.00
        ]);

        // Criar produto
        $this->product = Product::create([
            'seller_id' => $this->seller->id,
            'category_id' => $this->category->id,
            'name' => 'Notebook Dell',
            'slug' => 'notebook-dell',
            'description' => 'Notebook Dell i5 8GB',
            'price' => 2500.00,
            'stock_quantity' => 10,
            'status' => 'active'
        ]);
    }

    public function test_guest_can_add_product_to_cart()
    {
        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Produto adicionado ao carrinho!');

        // Verificar se o item foi adicionado ao carrinho
        $cart = Cart::where('session_id', session()->getId())->first();
        $this->assertNotNull($cart);
        $this->assertEquals(5000, $cart->total_amount); // 2500 * 2
        $this->assertEquals(2, $cart->total_items);
    }

    public function test_logged_user_can_add_product_to_cart()
    {
        $this->actingAs($this->customer);

        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verificar se o item foi adicionado ao carrinho do usuário
        $cart = Cart::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(2500, $cart->total_amount);
        $this->assertEquals(1, $cart->total_items);
    }

    public function test_cannot_add_more_than_stock_quantity()
    {
        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 15 // Mais que o estoque (10)
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Produto não tem estoque suficiente.');
    }

    public function test_can_update_cart_item_quantity()
    {
        $this->actingAs($this->customer);

        // Adicionar item ao carrinho
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'total_amount' => 0,
            'total_items' => 0
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price,
            'total_price' => $this->product->price
        ]);

        // Atualizar quantidade
        $response = $this->patch(route('cart.update', $item), [
            'quantity' => 3
        ]);

        $response->assertJson([
            'success' => true,
            'item_total' => 7500, // 2500 * 3
        ]);

        $item->refresh();
        $this->assertEquals(3, $item->quantity);
        $this->assertEquals(7500, $item->total_price);
    }

    public function test_can_remove_item_from_cart()
    {
        $this->actingAs($this->customer);

        // Criar carrinho com item
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'total_amount' => 2500,
            'total_items' => 1
        ]);

        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 2500,
            'total_price' => 2500
        ]);

        // Remover item
        $response = $this->delete(route('cart.remove', $item));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item removido do carrinho!');

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
        
        $cart->refresh();
        $this->assertEquals(0, $cart->total_amount);
        $this->assertEquals(0, $cart->total_items);
    }

    public function test_can_clear_entire_cart()
    {
        $this->actingAs($this->customer);

        // Criar carrinho com múltiplos itens
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'total_amount' => 5000,
            'total_items' => 2
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 2500,
            'total_price' => 5000
        ]);

        // Limpar carrinho
        $response = $this->delete(route('cart.clear'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Carrinho limpo!');

        $this->assertDatabaseMissing('cart_items', ['cart_id' => $cart->id]);
        
        $cart->refresh();
        $this->assertEquals(0, $cart->total_amount);
        $this->assertEquals(0, $cart->total_items);
    }

    public function test_cart_page_displays_items_correctly()
    {
        $this->actingAs($this->customer);

        // Criar carrinho com item
        $cart = Cart::create([
            'user_id' => $this->customer->id,
            'total_amount' => 2500,
            'total_items' => 1
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 2500,
            'total_price' => 2500
        ]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('Notebook Dell');
        $response->assertSee('R$ 2.500,00');
        $response->assertSee('Carrinho de Compras');
    }

    public function test_adding_same_product_increases_quantity()
    {
        $this->actingAs($this->customer);

        // Adicionar produto primeira vez
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        // Adicionar mesmo produto novamente
        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 3
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $cart = Cart::where('user_id', $this->customer->id)->first();
        $item = $cart->items()->first();

        $this->assertEquals(5, $item->quantity); // 2 + 3
        $this->assertEquals(12500, $item->total_price); // 2500 * 5
    }

    public function test_inactive_product_cannot_be_added()
    {
        $this->product->update(['status' => 'inactive']);

        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Este produto não está disponível para compra.');
    }

    public function test_buy_now_redirects_to_checkout()
    {
        $this->actingAs($this->customer);

        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
            'buy_now' => true
        ]);

        $response->assertRedirect(route('checkout.index'));
    }

    public function test_cart_persists_after_login()
    {
        // Adicionar como visitante
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $sessionId = session()->getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();
        $this->assertNotNull($guestCart);

        // Fazer login
        $this->actingAs($this->customer);

        // TODO: Implementar merge de carrinho após login
        // Este teste pode precisar de lógica adicional no LoginController
    }
}
