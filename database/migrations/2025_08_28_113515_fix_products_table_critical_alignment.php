<?php
/**
 * Arquivo: database/migrations/2025_08_28_113515_fix_products_table_critical_alignment.php
 * Descrição: MIGRATION CRÍTICA - Alinhamento da tabela products com Dicionário de Dados
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 * 
 * RESOLVE 14 INCONSISTÊNCIAS CRÍTICAS IDENTIFICADAS NA REVISÃO RIGOROSA
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
        Schema::table('products', function (Blueprint $table) {
            // 1. RENOMEAR compare_price para compare_at_price (padrão do dicionário)
            $table->renameColumn('compare_price', 'compare_at_price');
            
            // 2. ADICIONAR campos faltantes conforme Dicionário de Dados
            $table->decimal('cost', 10, 2)->nullable()->after('compare_at_price')->comment('Custo do produto');
            $table->string('barcode', 100)->nullable()->after('sku')->comment('Código de barras');
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'backorder'])->default('in_stock')->after('stock_quantity');
            
            // 3. SUBSTITUIR dimensions JSON por campos individuais (padrão e-commerce)
            $table->decimal('length', 8, 2)->nullable()->after('weight')->comment('Comprimento em cm');
            $table->decimal('width', 8, 2)->nullable()->after('length')->comment('Largura em cm'); 
            $table->decimal('height', 8, 2)->nullable()->after('width')->comment('Altura em cm');
            
            // 4. ADICIONAR campos de controle de produto
            $table->boolean('featured')->default(false)->after('status')->comment('Produto em destaque');
            $table->boolean('digital')->default(false)->after('featured')->comment('Produto digital');
            $table->json('downloadable_files')->nullable()->after('digital')->comment('Arquivos para download');
            
            // 5. ADICIONAR campo SEO faltante
            $table->text('meta_keywords')->nullable()->after('meta_description')->comment('Palavras-chave SEO');
            
            // 6. ADICIONAR campos de métricas
            $table->integer('sales_count')->default(0)->after('views_count')->comment('Total de vendas');
            $table->decimal('rating_average', 3, 2)->default(0.00)->after('sales_count')->comment('Média de avaliações');
            $table->integer('rating_count')->default(0)->after('rating_average')->comment('Total de avaliações');
        });
        
        // 7. REMOVER campos que não estão no Dicionário de Dados
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'dimensions',           // Substituído por length/width/height
                'min_stock_alert',      // Não previsto no dicionário
                'track_stock',          // Não previsto no dicionário
                'allow_backorders',     // Substituído por stock_status
                'meta_data'             // Não previsto no dicionário
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Reverter adições
            $table->dropColumn([
                'cost', 'barcode', 'stock_status', 'length', 'width', 'height',
                'featured', 'digital', 'downloadable_files', 'meta_keywords',
                'sales_count', 'rating_average', 'rating_count'
            ]);
        });
        
        Schema::table('products', function (Blueprint $table) {
            // Reverter rename
            $table->renameColumn('compare_at_price', 'compare_price');
            
            // Restaurar campos removidos
            $table->json('dimensions')->nullable()->comment('Largura, altura, profundidade em cm');
            $table->integer('min_stock_alert')->default(5);
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_backorders')->default(false);
            $table->json('meta_data')->nullable()->comment('Dados extras em JSON');
        });
    }
};
