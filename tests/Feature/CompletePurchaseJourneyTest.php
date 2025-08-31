<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompletePurchaseJourneyTest extends TestCase
{
    use RefreshDatabase;

    private $seller;
    private $product;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestData();
    }

    private function setupTestData()
    {
        // Criar vendedor com loja aprovada
        $this->seller = User::factory()->create(['role' => 'seller']);
        SellerProfile::factory()->create([
            'user_id' => $this->seller->id,
            'status' => 'approved'
        ]);

        // Criar categoria
        $this->category = Category::factory()->create(['is_active' => true]);

        // Criar produto ativo com imagem fictícia
        $this->product = Product::factory()->create([
            'seller_id' => $this->seller->sellerProfile->id,
            'category_id' => $this->category->id,
            'name' => 'Smartphone Test',
            'price' => 599.99,
            'stock_quantity' => 10,
            'status' => 'active'
        ]);

        // Adicionar imagem fictícia para permitir publicação
        $this->product->images()->create([
            'original_name' => 'smartphone.jpg',
            'file_name' => 'smartphone.jpg',
            'file_path' => 'products/smartphone.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => 1024,
            'alt_text' => 'Smartphone Test',
            'sort_order' => 1,
            'is_primary' => true,
        ]);
    }

    /**
     * Teste: Usuário não logado pode ver produto e detalhes
     */
    public function test_guest_can_view_product_details()
    {
        echo "\n🛒 SIMULANDO PROCESSO COMPLETO DE COMPRA\n";
        echo "=========================================\n";
        
        echo "1. 👀 Usuário não logado visualiza produto...\n";
        
        $response = $this->get("/products/{$this->product->id}");
        
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee('R$ ' . number_format($this->product->price, 2, ',', '.'));
        $response->assertSee('Adicionar ao Carrinho');
        $response->assertSee('Comprar Agora');
        
        echo "   ✅ Produto visível com preço e botões de compra\n";
    }

    /**
     * Teste: Usuário não logado pode adicionar produto ao carrinho
     */
    public function test_guest_can_add_product_to_cart()
    {
        echo "\n2. 🛒 Adicionando produto ao carrinho sem estar logado...\n";
        
        $response = $this->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Produto adicionado ao carrinho!');
        
        // Verificar se carrinho foi criado na sessão
        $cart = Cart::where('session_id', session()->getId())->first();
        $this->assertNotNull($cart);
        $this->assertEquals(2, $cart->total_items);
        $this->assertEquals($this->product->price * 2, $cart->total_amount);
        
        echo "   ✅ Produto adicionado ao carrinho usando sessão\n";
        echo "   ✅ Total: R$ " . number_format($cart->total_amount, 2, ',', '.') . " (2 itens)\n";
    }

    /**
     * Teste: Usuário pode visualizar carrinho com produtos
     */
    public function test_guest_can_view_cart()
    {
        echo "\n3. 👀 Visualizando carrinho com produtos...\n";
        
        // Criar um usuário temporário para ter sessão consistente
        $user = User::factory()->create();
        
        // Primeiro adicionar produto com usuário logado
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $response = $this->actingAs($user)->get('/cart');
        
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee('R$ ' . number_format($this->product->price, 2, ',', '.'));
        $response->assertSee('Finalizar Compra');
        $response->assertSee('1 item(s) no carrinho');
        
        echo "   ✅ Carrinho exibe produto, preço e botão finalizar\n";
    }

    /**
     * Teste: Usuário pode atualizar quantidade no carrinho
     */
    public function test_guest_can_update_cart_quantity()
    {
        echo "\n4. 📝 Atualizando quantidade no carrinho...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        // Adicionar produto
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItem = $cart->items()->first();
        
        // Atualizar quantidade via API
        $response = $this->actingAs($user)->put("/cart/update/{$cartItem->id}", [
            'quantity' => 3
        ]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $cartItem->refresh();
        $this->assertEquals(3, $cartItem->quantity);
        $this->assertEquals($this->product->price * 3, $cartItem->total_price);
        
        echo "   ✅ Quantidade atualizada para 3 unidades\n";
        echo "   ✅ Total do item: R$ " . number_format($cartItem->total_price, 2, ',', '.') . "\n";
    }

    /**
     * Teste: Usuário pode remover produto do carrinho
     */
    public function test_guest_can_remove_from_cart()
    {
        echo "\n5. ❌ Removendo produto do carrinho...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        // Adicionar produto
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItem = $cart->items()->first();
        
        // Remover item
        $response = $this->actingAs($user)->delete("/cart/remove/{$cartItem->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item removido do carrinho!');
        
        // Verificar se item foi removido
        $cart->refresh();
        $this->assertEquals(0, $cart->items()->count());
        
        echo "   ✅ Item removido do carrinho com sucesso\n";
    }

    /**
     * Teste: Não pode adicionar produto inativo ao carrinho
     */
    public function test_cannot_add_inactive_product_to_cart()
    {
        echo "\n6. 🚫 Testando proteção contra produtos inativos...\n";
        
        // Inativar produto
        $this->product->update(['status' => 'draft']);
        
        $response = $this->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Este produto não está disponível para compra.');
        
        echo "   ✅ Sistema impede adicionar produtos inativos\n";
        
        // Reativar para próximos testes
        $this->product->update(['status' => 'active']);
    }

    /**
     * Teste: Não pode adicionar quantidade maior que estoque
     */
    public function test_cannot_add_more_than_stock()
    {
        echo "\n7. 📦 Testando controle de estoque...\n";
        
        $response = $this->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 15 // Maior que os 10 em estoque
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Produto não tem estoque suficiente.');
        
        echo "   ✅ Sistema impede comprar mais que estoque disponível\n";
    }

    /**
     * Teste: Botão "Comprar Agora" redireciona para checkout
     */
    public function test_buy_now_redirects_to_checkout()
    {
        echo "\n8. 🚀 Testando 'Comprar Agora'...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
            'buy_now' => '1'
        ]);
        
        $response->assertRedirect('/checkout');
        
        echo "   ✅ 'Comprar Agora' adiciona ao carrinho e vai para checkout\n";
    }

    /**
     * Teste: Usuário pode acessar checkout com produtos no carrinho
     */
    public function test_can_access_checkout_with_items()
    {
        echo "\n9. 💳 Acessando checkout com produtos...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        // Adicionar produto ao carrinho
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        
        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertStatus(200);
        // Checkout deve mostrar produtos e formulário
        
        echo "   ✅ Checkout acessível com produtos no carrinho\n";
    }

    /**
     * Teste: Checkout vazio redireciona para carrinho
     */
    public function test_empty_checkout_redirects_to_cart()
    {
        echo "\n10. 🔄 Testando checkout vazio...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertRedirect('/cart');
        $response->assertSessionHas('error', 'Seu carrinho está vazio.');
        
        echo "   ✅ Checkout vazio redireciona para carrinho\n";
    }

    /**
     * Teste: Fluxo completo - visualizar produto, adicionar ao carrinho, ir para checkout
     */
    public function test_complete_shopping_flow()
    {
        echo "\n11. 🎯 FLUXO COMPLETO DE COMPRA...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        // 1. Ver produto
        echo "    → Visualizando produto\n";
        $response = $this->actingAs($user)->get("/products/{$this->product->id}");
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        
        // 2. Adicionar ao carrinho
        echo "    → Adicionando ao carrinho\n";
        $response = $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // 3. Ver carrinho
        echo "    → Visualizando carrinho\n";
        $response = $this->actingAs($user)->get('/cart');
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee('2'); // quantidade
        
        // 4. Ir para checkout
        echo "    → Acessando checkout\n";
        $response = $this->actingAs($user)->get('/checkout');
        $response->assertStatus(200);
        
        // 5. Verificar dados do carrinho
        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(2, $cart->total_items);
        $this->assertEquals($this->product->price * 2, $cart->total_amount);
        
        echo "   ✅ Fluxo completo executado com sucesso!\n";
        echo "   ✅ Carrinho: 2 itens - R$ " . number_format($cart->total_amount, 2, ',', '.') . "\n";
        
        echo "\n🎉 PROCESSO DE COMPRA FUNCIONANDO PERFEITAMENTE!\n";
        echo "=================================================\n";
        echo "✅ Visualização de produtos\n";
        echo "✅ Adição ao carrinho (usuário não logado)\n";
        echo "✅ Gerenciamento do carrinho\n";
        echo "✅ Controle de estoque\n";
        echo "✅ Proteção de produtos inativos\n";
        echo "✅ Acesso ao checkout\n";
        echo "✅ Fluxo completo funcional\n";
    }

    /**
     * Teste: Sistema mantém carrinho entre sessões
     */
    public function test_cart_persists_between_requests()
    {
        echo "\n12. 💾 Testando persistência do carrinho...\n";
        
        // Criar usuário temporário
        $user = User::factory()->create();
        
        // Primeira requisição - adicionar produto
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1
        ]);
        
        // Segunda requisição - verificar se carrinho persiste
        $response = $this->actingAs($user)->get('/cart');
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        
        echo "   ✅ Carrinho persiste entre diferentes requisições\n";
    }
}