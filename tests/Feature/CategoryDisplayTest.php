<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_exist_in_database()
    {
        echo "\n🔍 DIAGNÓSTICO COMPLETO DE CATEGORIAS\n";
        echo "====================================\n";
        
        $categories = Category::all();
        
        echo "1. 📊 TOTAL DE CATEGORIAS: " . $categories->count() . "\n";
        
        if ($categories->count() === 0) {
            echo "   ❌ NENHUMA CATEGORIA ENCONTRADA!\n";
            echo "   💡 Execute: php artisan db:seed para criar categorias\n";
            return;
        }
        
        echo "\n2. 📋 PRIMEIRAS 10 CATEGORIAS:\n";
        foreach ($categories->take(10) as $category) {
            $imageStatus = $category->image_path ? '✅' : '❌';
            echo "   {$imageStatus} {$category->name} ({$category->slug})\n";
            echo "       Parent: " . ($category->parent_id ? "ID {$category->parent_id}" : "NULO (principal)") . "\n";
            echo "       Image: " . ($category->image_path ?? 'NENHUMA') . "\n";
            echo "       Active: " . ($category->is_active ? 'SIM' : 'NÃO') . "\n";
            echo "\n";
        }
        
        $this->assertGreaterThan(0, $categories->count());
    }
    
    public function test_main_categories_exist()
    {
        $mainCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->get();
        
        echo "\n3. 🏠 CATEGORIAS PRINCIPAIS (parent_id = null):\n";
        echo "   Total: " . $mainCategories->count() . "\n";
        
        if ($mainCategories->count() === 0) {
            echo "   ❌ NENHUMA CATEGORIA PRINCIPAL ENCONTRADA!\n";
            echo "   💡 Todas as categorias têm parent_id preenchido?\n";
            
            // Mostrar algumas categorias com parent_id
            $withParent = Category::whereNotNull('parent_id')->take(5)->get();
            echo "   📋 Categorias com parent_id:\n";
            foreach ($withParent as $cat) {
                echo "       - {$cat->name} (parent_id: {$cat->parent_id})\n";
            }
        } else {
            foreach ($mainCategories as $category) {
                $imageStatus = $category->image_path ? '✅' : '❌';
                echo "   {$imageStatus} {$category->name}\n";
            }
        }
        
        return $mainCategories;
    }
    
    public function test_home_controller_logic()
    {
        echo "\n4. 🏠 TESTANDO LÓGICA DO HOME CONTROLLER:\n";
        
        // Simular exatamente o que o HomeController faz
        $mainCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->withCount(['products' => function($subQuery) {
                    $subQuery->where('status', 'active');
                }]);
            }])
            ->orderBy('sort_order')
            ->get();
            
        echo "   📊 Query do HomeController retornou: " . $mainCategories->count() . " categorias\n";
        
        // Calcular total de produtos (como no HomeController)
        $mainCategories->each(function($category) {
            $category->products_count = $category->children->sum('products_count');
            echo "   📦 {$category->name}: {$category->products_count} produtos\n";
        });
        
        return $mainCategories;
    }
    
    public function test_home_page_actually_shows_categories()
    {
        echo "\n5. 🌐 TESTANDO HOMEPAGE REAL:\n";
        
        $response = $this->get('/');
        
        if ($response->status() !== 200) {
            echo "   ❌ ERRO HTTP: " . $response->status() . "\n";
            return;
        }
        
        echo "   ✅ Homepage carregou (HTTP 200)\n";
        
        // Verificar se o texto base do category-grid aparece
        if (strpos($response->content(), 'Explore por') !== false) {
            echo "   ✅ Encontrou texto 'Explore por'\n";
        } else {
            echo "   ❌ NÃO encontrou texto 'Explore por'\n";
        }
        
        // Verificar debug que adicionamos
        if (strpos($response->content(), 'Debug:') !== false) {
            echo "   ✅ Debug do category-grid aparece na página\n";
        } else {
            echo "   ❌ Debug NÃO aparece (component não está sendo renderizado?)\n";
        }
        
        // Verificar se alguma categoria específica aparece
        $testCategory = Category::where('is_active', true)->whereNull('parent_id')->first();
        if ($testCategory) {
            if (strpos($response->content(), $testCategory->name) !== false) {
                echo "   ✅ Categoria '{$testCategory->name}' aparece na página\n";
            } else {
                echo "   ❌ Categoria '{$testCategory->name}' NÃO aparece na página\n";
            }
        }
        
        // Salvar HTML para inspeção manual se necessário
        file_put_contents(storage_path('logs/homepage-debug.html'), $response->content());
        echo "   💾 HTML salvo em: storage/logs/homepage-debug.html\n";
    }
    
    public function test_create_sample_categories_if_none()
    {
        echo "\n6. 🔧 CRIANDO CATEGORIAS DE TESTE:\n";
        
        // Verificar se já existem categorias principais
        $existing = Category::whereNull('parent_id')->count();
        
        if ($existing > 0) {
            echo "   ✅ Já existem {$existing} categorias principais\n";
            return;
        }
        
        echo "   📝 Criando categorias principais de teste...\n";
        
        $categories = [
            ['name' => 'Eletrônicos', 'slug' => 'eletronicos', 'sort_order' => 1],
            ['name' => 'Moda e Vestuário', 'slug' => 'moda-e-vestuario', 'sort_order' => 2],
            ['name' => 'Casa e Jardim', 'slug' => 'casa-e-jardim', 'sort_order' => 3],
            ['name' => 'Esportes', 'slug' => 'esportes', 'sort_order' => 4],
        ];
        
        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData + [
                'is_active' => true,
                'parent_id' => null,
                'description' => 'Categoria criada para teste'
            ]);
            
            echo "   ✅ Criada: {$category->name} (ID: {$category->id})\n";
        }
        
        echo "\n   🎯 Execute novamente para ver se as categorias aparecem!\n";
    }
}