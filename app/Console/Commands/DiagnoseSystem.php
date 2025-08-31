<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DiagnoseSystem extends Command
{
    protected $signature = 'system:diagnose';
    protected $description = 'Diagnóstico completo do sistema e tests';

    public function handle()
    {
        $this->info('🚨 DIAGNÓSTICO URGENTE DO SISTEMA');
        $this->info('===================================');

        // 1. Verificar banco de dados
        $this->checkDatabase();

        // 2. Verificar models e dados
        $this->checkModels();

        // 3. Verificar categorias especificamente
        $this->checkCategories();

        // 4. Executar tests críticos
        $this->runCriticalTests();

        // 5. Verificar arquivos importantes
        $this->checkFiles();

        $this->info('');
        $this->info('🎯 RESUMO E AÇÕES RECOMENDADAS:');
        $this->showRecommendations();
    }

    private function checkDatabase()
    {
        $this->info('');
        $this->info('1. 🔍 VERIFICANDO BANCO DE DADOS:');

        try {
            DB::connection()->getPdo();
            $this->info('   ✅ Conexão: OK');

            $tables = ['categories', 'users', 'products', 'seller_profiles', 'migrations'];
            foreach ($tables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->info("   📊 {$table}: {$count} registros");
                } catch (\Exception $e) {
                    $this->error("   ❌ {$table}: ERRO - " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Conexão falhou: ' . $e->getMessage());
        }
    }

    private function checkModels()
    {
        $this->info('');
        $this->info('2. 🏗️  VERIFICANDO MODELS:');

        $models = [
            'Category' => Category::class,
            'User' => User::class,
            'Product' => Product::class,
            'SellerProfile' => SellerProfile::class
        ];

        foreach ($models as $name => $class) {
            try {
                $count = $class::count();
                $this->info("   ✅ {$name}: {$count} registros");
            } catch (\Exception $e) {
                $this->error("   ❌ {$name}: " . $e->getMessage());
            }
        }
    }

    private function checkCategories()
    {
        $this->info('');
        $this->info('3. 📂 DIAGNÓSTICO DETALHADO DE CATEGORIAS:');

        try {
            $total = Category::count();
            $this->info("   📊 Total: {$total}");

            if ($total === 0) {
                $this->error('   ❌ NENHUMA CATEGORIA! Execute: php artisan db:seed');
                return;
            }

            $principais = Category::whereNull('parent_id')->count();
            $ativas = Category::where('is_active', true)->count();
            $comImagem = Category::whereNotNull('image_path')->count();

            $this->info("   🏠 Principais (parent_id=null): {$principais}");
            $this->info("   ✅ Ativas: {$ativas}");
            $this->info("   🖼️  Com imagens: {$comImagem}");

            if ($principais === 0) {
                $this->warn('   ⚠️  Sem categorias principais - HomePage não exibirá categorias');
            }

            // Mostrar primeiras categorias principais
            $primeiras = Category::whereNull('parent_id')->take(5)->get();
            foreach ($primeiras as $cat) {
                $img = $cat->image_path ? '✅' : '❌';
                $this->info("   {$img} {$cat->name} (slug: {$cat->slug})");
            }

        } catch (\Exception $e) {
            $this->error('   ❌ Erro: ' . $e->getMessage());
        }
    }

    private function runCriticalTests()
    {
        $this->info('');
        $this->info('4. 🧪 EXECUTANDO TESTS CRÍTICOS:');

        try {
            // Test básico de category
            $this->info('   🔍 Testando criação de categoria...');
            
            $testCat = Category::create([
                'name' => 'Test Diagnose ' . time(),
                'slug' => 'test-diagnose-' . time(),
                'is_active' => true,
                'sort_order' => 999
            ]);

            if ($testCat->exists) {
                $this->info('   ✅ Categoria criada com sucesso (ID: ' . $testCat->id . ')');
                $testCat->delete(); // Limpar
                $this->info('   ✅ Categoria removida - Model funcionando');
            } else {
                $this->error('   ❌ Falha ao criar categoria de teste');
            }

        } catch (\Exception $e) {
            $this->error('   ❌ Erro nos tests: ' . $e->getMessage());
        }
    }

    private function checkFiles()
    {
        $this->info('');
        $this->info('5. 📁 VERIFICANDO ARQUIVOS IMPORTANTES:');

        $files = [
            'app/Models/Category.php',
            'app/Http/Controllers/HomeController.php',
            'resources/views/home.blade.php',
            'resources/views/components/category-grid.blade.php',
            'database/seeders/DatabaseSeeder.php',
            'routes/web.php'
        ];

        foreach ($files as $file) {
            if (file_exists(base_path($file))) {
                $this->info("   ✅ {$file}");
            } else {
                $this->error("   ❌ {$file} - NÃO EXISTE!");
            }
        }
    }

    private function showRecommendations()
    {
        $categoriesCount = Category::count();
        $mainCategoriesCount = Category::whereNull('parent_id')->count();

        if ($categoriesCount === 0) {
            $this->warn('🔧 EXECUTE: php artisan db:seed');
        }

        if ($mainCategoriesCount === 0) {
            $this->warn('🔧 EXECUTE: php artisan migrate:fresh --seed');
        }

        $this->warn('🔧 EXECUTE: php artisan categories:create-images');
        $this->warn('🔧 EXECUTE: php artisan test --stop-on-failure');
        $this->info('🌐 TESTE: http://localhost/marketplace-b2c/public/');
    }
}