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
    /**
     * Seed the application's database with MASSIVE data.
     */
    public function run(): void
    {
        echo "🚀 INICIANDO CARGA COMPLETA DO MARKETPLACE B2C\n";
        echo str_repeat("=", 60) . "\n\n";

        // Preservar usuários essenciais antes de limpar
        echo "🔒 Preservando usuários essenciais...\n";
        $this->call(PreserveUsersSeeder::class);

        // Limpar dados existentes (cuidado em produção!)
        if (app()->environment('local')) {
            echo "🧹 Limpando dados existentes...\n";
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Preservar usuários essenciais
            $essentialUsers = \App\Models\User::whereIn('email', [
                'admin@marketplace.com',
                'tech@marketplace.com', 
                'cliente@marketplace.com'
            ])->get();
            
            $essentialProfiles = \App\Models\SellerProfile::whereIn('user_id', 
                $essentialUsers->pluck('id')
            )->get();
            
            $tables = ['products', 'seller_profiles', 'categories', 'users'];
            foreach ($tables as $table) {
                if ($table === 'users') {
                    DB::table($table)->whereNotIn('email', [
                        'admin@marketplace.com',
                        'tech@marketplace.com',
                        'cliente@marketplace.com'
                    ])->delete();
                } elseif ($table === 'seller_profiles') {
                    DB::table($table)->whereNotIn('user_id', 
                        $essentialUsers->pluck('id')
                    )->delete();
                } else {
                    DB::table($table)->truncate();
                }
                echo "  ✅ Tabela {$table} limpa (preservando essenciais)\n";
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            echo "\n";
        }

        $startTime = microtime(true);

        // 1. CATEGORIAS (8 principais + 64 subcategorias = 72 total)
        echo "📂 FASE 1: Criando categorias...\n";
        $this->call(CategorySeeder::class);
        echo "\n";

        // 2. USUÁRIOS (1 admin + 10 sellers + 20 customers = 31 total)
        echo "👥 FASE 2: Criando usuários e sellers...\n";
        $this->call(UserSeeder::class);
        echo "\n";

        // 3. PRODUTOS (Aproximadamente 800-1200 produtos)
        echo "📦 FASE 3: Criando produtos em massa...\n";
        $this->call(ProductSeeder::class);
        echo "\n";

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
