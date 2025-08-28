<?php
/**
 * Arquivo: database/migrations/2025_08_28_031306_create_product_variations_table.php
 * Descrição: Migration para variações de produtos (tamanhos, cores, etc)
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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('Nome da variação: Tamanho, Cor, etc');
            $table->string('value')->comment('Valor da variação: M, Azul, etc');
            $table->decimal('price_adjustment', 8, 2)->default(0)->comment('Ajuste no preço (+/-)');
            $table->integer('stock_quantity')->default(0);
            $table->string('sku_suffix')->nullable()->comment('Sufixo do SKU para esta variação');
            $table->decimal('weight_adjustment', 8, 2)->default(0)->comment('Ajuste no peso em gramas (+/-)');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('meta_data')->nullable()->comment('Dados extras em JSON');
            $table->timestamps();
            
            // Indexes
            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'name']);
            $table->unique(['product_id', 'name', 'value'], 'product_variation_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
