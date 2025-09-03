<?php
/**
 * Arquivo: app/Console/Commands/SetupMessagingSystem.php
 * Descrição: Comando para configurar o sistema de mensagens
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SetupMessagingSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messaging:setup {--test : Criar dados de teste}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura o sistema de mensagens (migrations e dados de teste)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('  Configurando Sistema de Mensagens');
        $this->info('========================================');
        $this->newLine();

        // Verificar se as tabelas já existem
        $this->info('Verificando tabelas...');
        
        $tables = [
            'conversations' => 'Conversas',
            'messages' => 'Mensagens',
            'delivery_agreements' => 'Acordos de Entrega'
        ];

        $allExist = true;
        foreach ($tables as $table => $name) {
            if (Schema::hasTable($table)) {
                $this->line("✓ Tabela '{$table}' já existe");
            } else {
                $this->warn("✗ Tabela '{$table}' não encontrada");
                $allExist = false;
            }
        }

        if (!$allExist) {
            $this->newLine();
            $this->info('Executando migrations...');
            
            try {
                $this->call('migrate');
                $this->info('Migrations executadas com sucesso!');
            } catch (\Exception $e) {
                $this->error('Erro ao executar migrations: ' . $e->getMessage());
                return 1;
            }
        }

        // Verificar novamente
        $this->newLine();
        $this->info('Verificando estrutura das tabelas...');
        
        if (Schema::hasTable('conversations')) {
            $columns = Schema::getColumnListing('conversations');
            $this->info('Tabela conversations: ' . count($columns) . ' colunas');
            
            $count = DB::table('conversations')->count();
            $this->line("  → {$count} conversas no banco");
        }

        if (Schema::hasTable('messages')) {
            $columns = Schema::getColumnListing('messages');
            $this->info('Tabela messages: ' . count($columns) . ' colunas');
            
            $count = DB::table('messages')->count();
            $this->line("  → {$count} mensagens no banco");
        }

        if (Schema::hasTable('delivery_agreements')) {
            $columns = Schema::getColumnListing('delivery_agreements');
            $this->info('Tabela delivery_agreements: ' . count($columns) . ' colunas');
            
            $count = DB::table('delivery_agreements')->count();
            $this->line("  → {$count} acordos no banco");
        }

        // Criar dados de teste se solicitado
        if ($this->option('test')) {
            $this->newLine();
            $this->info('Criando dados de teste...');
            
            try {
                $this->createTestData();
                $this->info('Dados de teste criados com sucesso!');
            } catch (\Exception $e) {
                $this->error('Erro ao criar dados de teste: ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('========================================');
        $this->info('  Sistema de Mensagens Configurado!');
        $this->info('========================================');
        $this->newLine();
        $this->line('Próximos passos:');
        $this->line('1. Acesse uma página de produto');
        $this->line('2. Clique em "Conversar com o Vendedor"');
        $this->line('3. Envie uma mensagem');
        $this->line('4. Acesse "Minhas Conversas" no menu do usuário');
        
        return 0;
    }

    /**
     * Criar dados de teste
     */
    private function createTestData()
    {
        // Buscar um vendedor e um cliente
        $seller = \App\Models\User::where('role', 'seller')->first();
        $customer = \App\Models\User::where('role', 'customer')->first();
        
        if (!$seller || !$customer) {
            $this->warn('Não foram encontrados vendedor e cliente para criar conversa de teste.');
            $this->line('Certifique-se de ter pelo menos um usuário vendedor e um cliente.');
            return;
        }

        // Buscar um produto do vendedor
        $product = \App\Models\Product::where('seller_id', $seller->id)->first();
        
        // Criar conversa de teste
        $conversation = \App\Models\Conversation::create([
            'uuid' => \Str::uuid(),
            'customer_id' => $customer->id,
            'seller_id' => $seller->id,
            'product_id' => $product?->id,
            'subject' => 'Conversa de Teste - Sistema de Mensagens',
            'status' => 'active',
            'priority' => 'normal'
        ]);

        $this->line("Conversa criada entre {$customer->name} e {$seller->name}");

        // Criar algumas mensagens
        $messages = [
            [
                'sender_id' => $customer->id,
                'sender_type' => 'customer',
                'content' => 'Olá! Vi seu produto e gostaria de saber mais informações sobre a entrega.',
            ],
            [
                'sender_id' => $seller->id,
                'sender_type' => 'seller',
                'content' => 'Olá! Claro, posso te ajudar. Fazemos entrega em toda a região ou você pode retirar na loja.',
            ],
            [
                'sender_id' => $customer->id,
                'sender_type' => 'customer',
                'content' => 'Ótimo! Quanto fica o frete para o centro da cidade?',
            ],
            [
                'sender_id' => $seller->id,
                'sender_type' => 'seller',
                'content' => 'Para o centro, cobramos R$ 15,00 de frete. Ou se preferir, pode retirar sem custo.',
            ],
        ];

        foreach ($messages as $messageData) {
            \App\Models\Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $messageData['sender_id'],
                'sender_type' => $messageData['sender_type'],
                'content' => $messageData['content'],
                'type' => 'text',
                'status' => 'sent'
            ]);
            
            sleep(1); // Pequena pausa para diferenciar timestamps
        }

        $this->line("4 mensagens de teste criadas");

        // Criar uma proposta de entrega
        $subOrder = \App\Models\SubOrder::where('seller_id', $seller->id)->first();
        
        if ($subOrder) {
            $agreement = \App\Models\DeliveryAgreement::create([
                'conversation_id' => $conversation->id,
                'sub_order_id' => $subOrder->id,
                'proposed_by' => $seller->id,
                'type' => 'pickup',
                'description' => 'Retirada na loja - Av. Principal, 123',
                'details' => [
                    'address' => 'Av. Principal, 123 - Centro',
                    'hours' => 'Segunda a Sexta, 9h às 18h',
                    'contact' => '(11) 98765-4321'
                ],
                'delivery_fee' => 0.00,
                'estimated_date' => now()->addDays(2),
                'estimated_time' => '14:00 - 18:00',
                'status' => 'proposed'
            ]);

            $this->line("Proposta de entrega criada");
        }
    }
}
