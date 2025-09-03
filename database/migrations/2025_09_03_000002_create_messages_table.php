<?php
/**
 * Arquivo: database/migrations/2025_01_03_000002_create_messages_table.php
 * Descrição: Migration para mensagens entre usuários e vendedores
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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->enum('sender_type', ['customer', 'seller', 'system'])->comment('Tipo do remetente');
            $table->text('content')->comment('Conteúdo da mensagem');
            $table->enum('type', ['text', 'image', 'document', 'delivery_proposal', 'system'])->default('text');
            $table->json('attachments')->nullable()->comment('Arquivos anexados');
            $table->json('delivery_info')->nullable()->comment('Informações de entrega quando type=delivery_proposal');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read', 'deleted'])->default('sent');
            $table->timestamps();
            
            // Índices para performance
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'sender_type']);
            $table->index(['conversation_id', 'is_read']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
