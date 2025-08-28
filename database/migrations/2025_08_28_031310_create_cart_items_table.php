<?php
/**
 * Arquivo: database/migrations/2025_08_28_031316_create_cart_items_table.php
 * Descrição: Migration para itens do carrinho de compras
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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variation_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->comment('Preço unitário no momento da adição');
            $table->decimal('total_price', 10, 2)->comment('Preço total (quantity * unit_price)');
            $table->json('product_snapshot')->nullable()->comment('Snapshot dos dados do produto');
            $table->json('variation_snapshot')->nullable()->comment('Snapshot da variação');
            $table->timestamps();
            
            // Indexes
            $table->index(['cart_id', 'product_id']);
            $table->unique(['cart_id', 'product_id', 'product_variation_id'], 'cart_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
