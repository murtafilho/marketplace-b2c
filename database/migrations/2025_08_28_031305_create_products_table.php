<?php
/**
 * Arquivo: database/migrations/2025_08_28_031306_create_products_table.php
 * Descrição: Migration para tabela de produtos do marketplace
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable()->comment('Preço antes do desconto');
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->string('sku')->nullable();
            $table->decimal('weight', 8, 2)->nullable()->comment('Peso em gramas');
            $table->json('dimensions')->nullable()->comment('Largura, altura, profundidade em cm');
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft');
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_backorders')->default(false);
            $table->json('meta_data')->nullable()->comment('Dados extras em JSON');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->integer('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['seller_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index('slug');
            $table->index('status');
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
