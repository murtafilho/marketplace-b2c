<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class BuyerJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected $buyer;
    protected $seller;
    protected $sellerProfile;
    protected $category;
    protected $products;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário comprador
        $this->buyer = User::factory()->create([
            'name' => 'Comprador Teste',
            'email' => 'comprador@teste.com',
            'role' => 'customer'
        ]);

        // Criar vendedor
        $this->seller = User::factory()->create([
            'name' => 'Vendedor Teste',
            'email' => 'vendedor@teste.com',
            'role' => 'seller'
        ]);

        // Criar perfil do vendedor aprovado
        $this->sellerProfile = SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'approved',
            'company_name' => 'Loja Teste',
            'commission_rate' => 10.0
        ]);

        // Criar categoria
        $this->category = Category::factory()->create([
            'name' => 'Eletrônicos',
            'is_active' => true
        ]);

        // Criar produtos
        $this->products = collect([
            Product::factory()->create([
                'seller_id' => $this->sellerProfile->id,
                'category_id' => $this->category->id,
                'name' => 'Smartphone XYZ',
                'price' => 999.99,
                'stock_quantity' => 10,
                'status' => 'active'
            ]),
            Product::factory()->create([
                'seller_id' => $this->sellerProfile->id,
                'category_id' => $this->category->id,
                'name' => 'Notebook ABC',
                'price' => 2499.99,
                'stock_quantity' => 5,
                'status' => 'active'
            ])
        ]);
    }

    public function test_buyer_can_browse_homepage()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Eletrônicos');
        $response->assertSee('Smartphone XYZ');
        $response->assertSee('Notebook ABC');
        $response->assertSee('R$ 999,99');
        $response->assertSee('R$ 2.499,99');
    }

    public function test_buyer_can_search_products()
    {
        $response = $this->get('/?search=smartphone');
        
        $response->assertStatus(200);
        $response->assertSee('Smartphone XYZ');
        $response->assertDontSee('Notebook ABC');
    }

    public function test_buyer_can_view_category_products()
    {
        $response = $this->get("/products/category/{$this->category->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Eletrônicos');
        $response->assertSee('Smartphone XYZ');
        $response->assertSee('Notebook ABC');
    }

    public function test_buyer_can_view_product_details()
    {
        $product = $this->products->first();
        
        $response = $this->get("/products/{$product->id}");
        
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('R$ ' . number_format($product->price, 2, ',', '.'));
        $response->assertSee($product->description);
        $response->assertSee('Adicionar ao Carrinho');
        $response->assertSee("Vendido por: {$this->sellerProfile->company_name}");
    }

    public function test_guest_can_add_product_to_cart()
    {
        $product = $this->products->first();
        
        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verificar se foi adicionado à sessão
        $cart = Session::get('cart', []);
        $this->assertArrayHasKey($product->id, $cart);
        $this->assertEquals(2, $cart[$product->id]['quantity']);
    }

    public function test_buyer_can_view_cart()
    {
        $product = $this->products->first();
        
        // Adicionar ao carrinho
        $this->withSession([
            'cart' => [
                $product->id => [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => $product->price
                ]
            ]
        ]);
        
        $response = $this->get('/cart');
        
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('2'); // quantidade
        $response->assertSee('R$ ' . number_format($product->price * 2, 2, ',', '.')); // total
        $response->assertSee('Finalizar Compra');
    }

    public function test_buyer_can_update_cart_quantity()
    {
        $product = $this->products->first();
        
        // Adicionar ao carrinho
        $this->withSession([
            'cart' => [
                $product->id => [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => $product->price
                ]
            ]
        ]);
        
        $response = $this->put("/cart/update/{$product->id}", [
            'quantity' => 3
        ]);
        
        $response->assertRedirect();
        
        // Verificar se a quantidade foi atualizada
        $cart = Session::get('cart', []);
        $this->assertEquals(3, $cart[$product->id]['quantity']);
    }

    public function test_buyer_can_remove_item_from_cart()
    {
        $product = $this->products->first();
        
        // Adicionar ao carrinho
        $this->withSession([
            'cart' => [
                $product->id => [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'price' => $product->price
                ]
            ]
        ]);
        
        $response = $this->delete("/cart/remove/{$product->id}");
        
        $response->assertRedirect();
        
        // Verificar se foi removido
        $cart = Session::get('cart', []);
        $this->assertArrayNotHasKey($product->id, $cart);
    }

    public function test_guest_must_login_to_checkout()
    {
        $product = $this->products->first();
        
        // Adicionar ao carrinho
        $this->withSession([
            'cart' => [
                $product->id => [
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price
                ]
            ]
        ]);
        
        $response = $this->get('/checkout');
        
        $response->assertRedirect('/login');
    }

    public function test_authenticated_buyer_can_access_checkout()
    {
        $product = $this->products->first();
        
        // Adicionar ao carrinho e fazer login
        $this->actingAs($this->buyer)
             ->withSession([
                 'cart' => [
                     $product->id => [
                         'product_id' => $product->id,
                         'quantity' => 1,
                         'price' => $product->price
                     ]
                 ]
             ]);
        
        $response = $this->get('/checkout');
        
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Informações de Entrega');
        $response->assertSee('Finalizar Pedido');
    }

    public function test_buyer_cannot_checkout_with_empty_cart()
    {
        $response = $this->actingAs($this->buyer)
                         ->get('/checkout');
        
        $response->assertRedirect('/cart');
        $response->assertSessionHas('error');
    }

    public function test_buyer_can_complete_checkout()
    {
        $product = $this->products->first();
        
        // Adicionar ao carrinho e fazer login
        $this->actingAs($this->buyer)
             ->withSession([
                 'cart' => [
                     $product->id => [
                         'product_id' => $product->id,
                         'quantity' => 1,
                         'price' => $product->price
                     ]
                 ]
             ]);
        
        $checkoutData = [
            'shipping_address' => 'Rua Teste, 123',
            'shipping_city' => 'São Paulo',
            'shipping_state' => 'SP',
            'shipping_postal_code' => '01234-567',
            'shipping_method' => 'standard',
            'payment_method' => 'mercadopago'
        ];
        
        $response = $this->post('/checkout/process', $checkoutData);
        
        $response->assertRedirect();
        
        // Verificar se o pedido foi criado
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->buyer->id,
            'status' => 'pending',
            'total' => $product->price,
            'shipping_address' => 'Rua Teste, 123'
        ]);
        
        // Verificar se os itens do pedido foram criados
        $order = Order::where('user_id', $this->buyer->id)->first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price
        ]);
    }

    public function test_buyer_can_view_order_confirmation()
    {
        // Criar um pedido
        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'status' => 'pending',
            'total' => 999.99
        ]);
        
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->products->first()->id,
            'quantity' => 1,
            'price' => 999.99
        ]);
        
        $response = $this->actingAs($this->buyer)
                         ->get("/checkout/success/{$order->id}");
        
        $response->assertStatus(200);
        $response->assertSee('Pedido Confirmado');
        $response->assertSee($order->id);
        $response->assertSee('R$ 999,99');
        $response->assertSee($this->products->first()->name);
    }

    public function test_buyer_can_view_order_history()
    {
        // Criar alguns pedidos
        $orders = Order::factory(3)->create([
            'user_id' => $this->buyer->id
        ]);
        
        foreach ($orders as $order) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $this->products->first()->id
            ]);
        }
        
        $response = $this->actingAs($this->buyer)
                         ->get('/orders');
        
        $response->assertStatus(200);
        $response->assertSee('Meus Pedidos');
        
        foreach ($orders as $order) {
            $response->assertSee($order->id);
            $response->assertSee($order->status);
        }
    }

    public function test_buyer_cannot_checkout_out_of_stock_product()
    {
        $product = $this->products->first();
        $product->update(['stock_quantity' => 0]);
        
        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        
        $response->assertSessionHasErrors(['product_id']);
    }

    public function test_buyer_cannot_add_more_than_available_stock()
    {
        $product = $this->products->first();
        $product->update(['stock_quantity' => 3]);
        
        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 5
        ]);
        
        $response->assertSessionHasErrors(['quantity']);
    }

    public function test_product_stock_is_reduced_after_purchase()
    {
        $product = $this->products->first();
        $initialStock = $product->stock_quantity;
        
        // Simular compra
        $this->actingAs($this->buyer)
             ->withSession([
                 'cart' => [
                     $product->id => [
                         'product_id' => $product->id,
                         'quantity' => 2,
                         'price' => $product->price
                     ]
                 ]
             ]);
        
        $checkoutData = [
            'shipping_address' => 'Rua Teste, 123',
            'shipping_city' => 'São Paulo',
            'shipping_state' => 'SP',
            'shipping_postal_code' => '01234-567',
            'shipping_method' => 'standard',
            'payment_method' => 'mercadopago'
        ];
        
        $this->post('/checkout/process', $checkoutData);
        
        // Verificar se o estoque foi reduzido
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock_quantity);
    }

    public function test_cart_is_cleared_after_successful_checkout()
    {
        $product = $this->products->first();
        
        $this->actingAs($this->buyer)
             ->withSession([
                 'cart' => [
                     $product->id => [
                         'product_id' => $product->id,
                         'quantity' => 1,
                         'price' => $product->price
                     ]
                 ]
             ]);
        
        $checkoutData = [
            'shipping_address' => 'Rua Teste, 123',
            'shipping_city' => 'São Paulo',
            'shipping_state' => 'SP',
            'shipping_postal_code' => '01234-567',
            'shipping_method' => 'standard',
            'payment_method' => 'mercadopago'
        ];
        
        $this->post('/checkout/process', $checkoutData);
        
        // Verificar se o carrinho foi limpo
        $this->assertEmpty(Session::get('cart', []));
    }

    public function test_complete_buyer_journey()
    {
        // 1. Navegar pela homepage
        $response = $this->get('/');
        $response->assertStatus(200);
        
        // 2. Buscar produto
        $product = $this->products->first();
        $response = $this->get('/?search=smartphone');
        $response->assertSee($product->name);
        
        // 3. Ver detalhes do produto
        $response = $this->get("/products/{$product->id}");
        $response->assertStatus(200);
        
        // 4. Adicionar ao carrinho
        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $response->assertRedirect();
        
        // 5. Ver carrinho
        $response = $this->get('/cart');
        $response->assertSee($product->name);
        
        // 6. Fazer login
        $this->actingAs($this->buyer);
        
        // 7. Ir para checkout
        $response = $this->get('/checkout');
        $response->assertStatus(200);
        
        // 8. Finalizar compra
        $response = $this->post('/checkout/process', [
            'shipping_address' => 'Rua Teste, 123',
            'shipping_city' => 'São Paulo',
            'shipping_state' => 'SP',
            'shipping_postal_code' => '01234-567',
            'shipping_method' => 'standard',
            'payment_method' => 'mercadopago'
        ]);
        
        // 9. Verificar se pedido foi criado
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->buyer->id,
            'status' => 'pending'
        ]);
        
        // 10. Ver confirmação
        $order = Order::where('user_id', $this->buyer->id)->first();
        $response = $this->get("/checkout/success/{$order->id}");
        $response->assertStatus(200);
        $response->assertSee('Pedido Confirmado');
    }
}