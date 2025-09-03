<?php
/**
 * Arquivo: database/migrations/2025_01_03_000001_create_conversations_table.php
 * Descrição: Migration para conversas entre usuários e vendedores
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique()->comment('UUID único para a conversa');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('seller_profiles')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null')->comment('Produto relacionado (se houver)');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null')->comment('Pedido relacionado (se houver)');
            $table->string('subject')->nullable()->comment('Assunto da conversa');
            $table->enum('status', ['active', 'archived', 'blocked'])->default('active');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal')->comment('Prioridade para o vendedor');
            $table->timestamp('last_message_at')->nullable()->comment('Data da última mensagem');
            $table->foreignId('last_message_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('unread_customer')->default(0)->comment('Mensagens não lidas pelo cliente');
            $table->integer('unread_seller')->default(0)->comment('Mensagens não lidas pelo vendedor');
            $table->json('metadata')->nullable()->comment('Dados adicionais (ex: info de entrega combinada)');
            $table->timestamps();
            
            // Índices para performance
            $table->index(['customer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['status', 'last_message_at']);
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
