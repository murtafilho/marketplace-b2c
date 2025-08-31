<?php
/**
 * Arquivo: database/seeders/DatabaseSeeder.php
 * Descrição: Seeder principal - CARGA COMPLETA DO MARKETPLACE
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private bool $skipMassData = false;
    
    /**
     * Seed the application's database with CONSERVATIVE data.
     */
    public function run(): void
    {
        echo "🚀 INICIANDO CARGA COMPLETA DO MARKETPLACE B2C\n";
        echo str_repeat("=", 60) . "\n\n";

        // SEMPRE garantir que usuários protegidos existam
        echo "🔒 Garantindo usuários protegidos...\n";
        $this->call(ProtectedUsersSeeder::class);
        
        // SEMPRE garantir que configurações de layout existam
        echo "🎨 Garantindo configurações de layout...\n";
        $this->call(LayoutSeeder::class);

        // MODO CONSERVADOR: Apenas adicionar dados se necessário
        echo "🛡️  MODO CONSERVADOR: Preservando dados existentes...\n";
        
        // Verificar se já existem dados
        $existingUsers = \App\Models\User::count();
        $existingCategories = \App\Models\Category::count();
        $existingProducts = \App\Models\Product::count();
        
        echo "📊 Dados existentes: {$existingUsers} users, {$existingCategories} categorias, {$existingProducts} produtos\n";
        
        if ($existingUsers > 3 && $existingCategories > 5 && $existingProducts > 0) {
            echo "✅ Sistema já possui dados suficientes. Pulando criação em massa.\n";
            echo "💡 Use 'php artisan migrate:fresh --seed' apenas se quiser resetar completamente.\n\n";
            $this->skipMassData = true;
        } else {
            echo "🔧 Dados insuficientes. Criando apenas o essencial...\n\n";
            $this->skipMassData = false;
        }

        $startTime = microtime(true);

        // 1. CATEGORIAS (apenas se necessário)
        if (\App\Models\Category::count() < 5) {
            echo "📂 FASE 1: Criando categorias essenciais...\n";
            $this->call(CategorySeeder::class);
            echo "\n";
        } else {
            echo "📂 FASE 1: Categorias já existem - pulando\n\n";
        }

        // 2. USUÁRIOS (apenas se necessário)  
        if (\App\Models\User::count() < 3) {
            echo "👥 FASE 2: Criando usuários essenciais...\n";
            $this->call(UserSeeder::class);
            echo "\n";
        } else {
            echo "👥 FASE 2: Usuários já existem - pulando\n\n";
        }

        // 3. DADOS EM MASSA (apenas se solicitado e necessário)
        if (!$this->skipMassData) {
            echo "📦 FASE 3: Criando dados mínimos...\n";
            $this->call(MassDataSeeder::class);
            echo "\n";
        } else {
            echo "📦 FASE 3: Dados em massa pulados - sistema já populado\n\n";
        }

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        // RESUMO FINAL
        $this->showFinalSummary($executionTime);
    }

    private function showFinalSummary(float $executionTime): void
    {
        echo "🎉 CARGA COMPLETA FINALIZADA!\n";
        echo str_repeat("=", 60) . "\n";
        
        // Contar registros
        $users = DB::table('users')->count();
        $sellers = DB::table('seller_profiles')->count();
        $categories = DB::table('categories')->count();
        $products = DB::table('products')->count();
        
        echo "📊 TOTAIS CRIADOS:\n";
        echo "├── Usuários: {$users}\n";
        echo "├── Sellers: {$sellers}\n";
        echo "├── Categorias: {$categories}\n";
        echo "└── Produtos: {$products}\n";
        echo "\n";
        
        // Estatísticas por role
        $admins = DB::table('users')->where('role', 'admin')->count();
        $sellersUsers = DB::table('users')->where('role', 'seller')->count();
        $customers = DB::table('users')->where('role', 'customer')->count();
        
        echo "👥 POR TIPO DE USUÁRIO:\n";
        echo "├── Admins: {$admins}\n";
        echo "├── Sellers: {$sellersUsers}\n";
        echo "└── Customers: {$customers}\n";
        echo "\n";
        
        // Estatísticas de produtos
        $activeProducts = DB::table('products')->where('status', 'active')->count();
        $featuredProducts = DB::table('products')->where('featured', true)->count();
        $inStockProducts = DB::table('products')->where('stock_status', 'in_stock')->count();
        
        echo "📦 PRODUTOS:\n";
        echo "├── Ativos: {$activeProducts}\n";
        echo "├── Em destaque: {$featuredProducts}\n";
        echo "└── Em estoque: {$inStockProducts}\n";
        echo "\n";
        
        // Performance
        echo "⏱️ PERFORMANCE:\n";
        echo "├── Tempo de execução: {$executionTime}s\n";
        echo "├── Registros/segundo: " . round(($users + $categories + $products) / $executionTime) . "\n";
        echo "└── Tamanho estimado do banco: " . $this->estimateDbSize() . "\n";
        echo "\n";
        
        // Credenciais de acesso
        echo "🔑 CREDENCIAIS DE ACESSO:\n";
        echo "├── Admin: admin@marketplace.com / admin123\n";
        echo "├── Seller: tech@marketplace.com / seller123\n";
        echo "├── Customer: cliente1@marketplace.com / cliente123\n";
        echo "└── URLs:\n";
        echo "    ├── Admin: http://marketplace-b2c.test/admin/dashboard\n";
        echo "    ├── Seller: http://marketplace-b2c.test/seller/dashboard\n";
        echo "    └── Home: http://marketplace-b2c.test/\n";
        echo "\n";
        
        echo "🎯 MARKETPLACE PRONTO PARA TESTES INTENSIVOS!\n";
        echo "🚀 Sistema com carga realística para validação completa.\n";
        echo str_repeat("=", 60) . "\n";
    }

    private function estimateDbSize(): string
    {
        try {
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS db_size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            
            return isset($result[0]) ? $result[0]->db_size_mb . ' MB' : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
