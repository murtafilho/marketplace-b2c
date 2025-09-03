<?php
/**
 * Arquivo: tests/Feature/MessagingSystemTest.php
 * Descrição: Testes do sistema de mensagens
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\DeliveryAgreement;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessagingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $seller;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar categoria
        $category = Category::create([
            'name' => 'Categoria Teste',
            'slug' => 'categoria-teste',
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
            'category_id' => $category->id,
            'name' => 'Produto Teste',
            'slug' => 'produto-teste',
            'description' => 'Descrição do produto',
            'price' => 100.00,
            'stock_quantity' => 10,
            'status' => 'active'
        ]);
    }

    public function test_customer_can_start_conversation_with_seller()
    {
        $this->actingAs($this->customer);

        $response = $this->post(route('conversations.store'), [
            'product_id' => $this->product->id,
            'seller_id' => $this->seller->id,
            'subject' => 'Dúvida sobre produto',
            'message' => 'Olá, gostaria de saber mais sobre este produto.'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('conversations', [
            'customer_id' => $this->customer->id,
            'seller_id' => $this->seller->id,
            'product_id' => $this->product->id,
            'subject' => 'Dúvida sobre produto'
        ]);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->customer->id,
            'sender_type' => 'customer',
            'content' => 'Olá, gostaria de saber mais sobre este produto.'
        ]);
    }

    public function test_seller_can_respond_to_conversation()
    {
        // Criar conversa
        $conversation = Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'seller_id' => $this->seller->id,
            'product_id' => $this->product->id,
            'subject' => 'Dúvida sobre produto',
            'status' => 'active'
        ]);

        // Vendedor responde
        $this->actingAs($this->seller);

        $response = $this->post(route('conversations.send-message', $conversation), [
            'content' => 'Olá! Este produto está disponível para pronta entrega.',
            'type' => 'text'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->seller->id,
            'sender_type' => 'seller',
            'content' => 'Olá! Este produto está disponível para pronta entrega.'
        ]);
    }

    public function test_user_can_view_conversations_list()
    {
        // Criar algumas conversas
        $conversation1 = Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'seller_id' => $this->seller->id,
            'subject' => 'Conversa 1',
            'status' => 'active'
        ]);

        $conversation2 = Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'seller_id' => $this->seller->id,
            'subject' => 'Conversa 2',
            'status' => 'active'
        ]);

        $this->actingAs($this->customer);

        $response = $this->get(route('conversations.index'));

        $response->assertStatus(200);
        $response->assertSee('Conversa 1');
        $response->assertSee('Conversa 2');
    }

    public function test_unread_messages_counter_works()
    {
        $conversation = Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'seller_id' => $this->seller->id,
            'subject' => 'Test',
            'status' => 'active',
            'unread_customer' => 0,
            'unread_seller' => 0
        ]);

        // Vendedor envia mensagem
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->seller->id,
            'sender_type' => 'seller',
            'content' => 'Nova mensagem',
            'type' => 'text',
            'is_read' => false
        ]);

        $conversation->refresh();
        
        // Cliente deve ter 1 mensagem não lida
        $this->assertEquals(1, $conversation->unread_customer);
        $this->assertEquals(0, $conversation->unread_seller);
    }

    public function test_user_cannot_access_others_conversations()
    {
        $otherUser = User::factory()->create(['role' => 'customer']);

        $conversation = Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $otherUser->id,
            'seller_id' => $this->seller->id,
            'subject' => 'Private conversation',
            'status' => 'active'
        ]);

        $this->actingAs($this->customer);

        $response = $this->get(route('conversations.show', $conversation));

        $response->assertStatus(403);
    }

    public function test_messages_are_marked_as_read_when_viewing_conversation()
    {
        $conversation = Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $this->customer->id,
            'seller_id' => $this->seller->id,
            'subject' => 'Test',
            'status' => 'active',
            'unread_customer' => 2
        ]);

        // Criar mensagens não lidas
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->seller->id,
            'sender_type' => 'seller',
            'content' => 'Mensagem 1',
            'type' => 'text',
            'is_read' => false
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->seller->id,
            'sender_type' => 'seller',
            'content' => 'Mensagem 2',
            'type' => 'text',
            'is_read' => false
        ]);

        $this->actingAs($this->customer);

        // Visualizar conversa
        $response = $this->get(route('conversations.show', $conversation));

        $response->assertStatus(200);

        // Verificar se mensagens foram marcadas como lidas
        $conversation->refresh();
        $this->assertEquals(0, $conversation->unread_customer);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'is_read' => true
        ]);
    }
}
