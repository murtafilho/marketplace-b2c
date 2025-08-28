<?php
/**
 * Arquivo: database/migrations/2025_08_28_031317_create_sub_orders_table.php
 * Descrição: Migration para sub-pedidos agrupados por vendedor
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
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
        Schema::create('sub_orders', function (Blueprint $table) {
            $table->id();
            $table->string('sub_order_number')->unique();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('seller_profiles')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled'
            ])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('seller_amount', 10, 2)->comment('Valor que o vendedor recebe');
            $table->decimal('commission_amount', 10, 2)->comment('Comissão do marketplace');
            $table->decimal('commission_rate', 5, 2)->comment('Taxa de comissão aplicada');
            $table->json('shipping_method')->nullable()->comment('Método de entrega escolhido');
            $table->json('tracking_info')->nullable()->comment('Informações de rastreamento');
            $table->text('seller_notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id', 'seller_id']);
            $table->index(['seller_id', 'status']);
            $table->index('sub_order_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_orders');
    }
};
