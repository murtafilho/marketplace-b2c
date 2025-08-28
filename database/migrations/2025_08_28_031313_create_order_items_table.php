<?php
/**
 * Arquivo: database/migrations/2025_08_28_031325_create_order_items_table.php
 * Descrição: Migration para itens dos pedidos
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variation_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('product_name')->comment('Nome do produto no momento da compra');
            $table->string('product_sku')->nullable()->comment('SKU do produto no momento da compra');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->comment('Preço unitário no momento da compra');
            $table->decimal('total_price', 10, 2)->comment('Preço total (quantity * unit_price)');
            $table->json('product_snapshot')->nullable()->comment('Snapshot completo do produto');
            $table->json('variation_snapshot')->nullable()->comment('Snapshot da variação escolhida');
            $table->decimal('commission_rate', 5, 2)->comment('Taxa de comissão aplicada');
            $table->decimal('commission_amount', 10, 2)->comment('Valor da comissão');
            $table->decimal('seller_amount', 10, 2)->comment('Valor que o vendedor recebe');
            $table->timestamps();
            
            // Indexes
            $table->index(['order_id', 'sub_order_id']);
            $table->index(['product_id', 'created_at']);
            $table->index('sub_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
