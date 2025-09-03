<?php
/**
 * Arquivo: database/migrations/2025_01_03_000003_create_delivery_agreements_table.php
 * Descrição: Migration para acordos de entrega entre vendedores e compradores
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
        Schema::create('delivery_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('proposed_by')->constrained('users')->comment('Quem propôs o acordo');
            $table->enum('type', [
                'pickup',           // Retirada no local
                'meet_location',    // Encontro em local combinado
                'custom_delivery',  // Entrega personalizada
                'correios',         // Correios (futuro)
                'transportadora'    // Transportadora (futuro)
            ]);
            $table->text('description')->comment('Descrição do acordo de entrega');
            $table->json('details')->nullable()->comment('Detalhes específicos (endereço, horário, etc)');
            $table->decimal('delivery_fee', 10, 2)->default(0.00)->comment('Taxa de entrega acordada');
            $table->date('estimated_date')->nullable()->comment('Data estimada de entrega/retirada');
            $table->string('estimated_time')->nullable()->comment('Horário estimado');
            $table->enum('status', [
                'proposed',     // Proposto
                'negotiating',  // Em negociação
                'accepted',     // Aceito por ambas as partes
                'rejected',     // Rejeitado
                'completed',    // Entrega realizada
                'cancelled'     // Cancelado
            ])->default('proposed');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('completion_proof')->nullable()->comment('Comprovante de entrega (fotos, assinatura, etc)');
            $table->timestamps();
            
            // Índices
            $table->index(['sub_order_id', 'status']);
            $table->index(['conversation_id', 'status']);
            $table->index('status');
            $table->index('estimated_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_agreements');
    }
};
