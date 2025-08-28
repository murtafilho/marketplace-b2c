<?php

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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('seller_profiles')->onDelete('cascade');
            $table->string('mp_payment_id')->nullable()->comment('ID do pagamento no Mercado Pago');
            $table->enum('type', ['payment', 'refund', 'commission', 'split'])->default('payment');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2)->comment('Taxa de comissão aplicada');
            $table->decimal('commission_amount', 10, 2)->comment('Valor da comissão');
            $table->decimal('seller_amount', 10, 2)->comment('Valor líquido para o vendedor');
            $table->string('mp_collector_id')->nullable()->comment('ID do recebedor no MP');
            $table->json('mp_response')->nullable()->comment('Resposta completa do MP');
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id', 'seller_id']);
            $table->index('mp_payment_id');
            $table->index(['status', 'type']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
