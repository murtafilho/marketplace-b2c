<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\TestUsersSeeder;

class CreateTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:test {--fresh : Recriar usuários mesmo se já existirem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar usuários de teste (admin, vendedor, cliente) para desenvolvimento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Marketplace B2C - Criação de Usuários de Teste');
        $this->line(str_repeat('=', 50));
        
        if ($this->option('fresh')) {
            $this->warn('⚠️  Modo FRESH: Removendo usuários de teste existentes...');
            $this->removeTestUsers();
        }

        $this->info('👨‍💻 Executando TestUsersSeeder...');
        $this->call('db:seed', ['--class' => TestUsersSeeder::class]);
        
        $this->line('');
        $this->info('✅ Usuários de teste criados com sucesso!');
        $this->line('');
        
        // Mostrar instruções de login
        $this->showLoginInstructions();
        
        return 0;
    }

    private function removeTestUsers(): void
    {
        // Remover usuários de teste específicos (com soft delete)
        $adminUser = \App\Models\User::withTrashed()->where('email', 'admin@marketplace.com')->first();
        if ($adminUser) {
            if ($adminUser->sellerProfile) {
                $adminUser->sellerProfile->forceDelete();
            }
            $adminUser->forceDelete();
        }
        
        $sellerUser = \App\Models\User::withTrashed()->where('email', 'vendedor@marketplace.com')->first();
        if ($sellerUser) {
            if ($sellerUser->sellerProfile) {
                $sellerUser->sellerProfile->forceDelete();
            }
            $sellerUser->forceDelete();
        }
        
        $customerUser = \App\Models\User::withTrashed()->where('email', 'cliente@marketplace.com')->first();
        if ($customerUser) {
            $customerUser->forceDelete();
        }
        
        // Remover categoria e produto de teste
        $category = \App\Models\Category::where('slug', 'eletronicos')->first();
        if ($category) {
            \App\Models\Product::where('category_id', $category->id)->forceDelete();
            $category->delete();
        }
        
        $this->info('🧹 Usuários de teste removidos permanentemente');
    }

    private function showLoginInstructions(): void
    {
        $this->line('📋 CREDENCIAIS PARA TESTE:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('<fg=red>👑 ADMIN:</fg=red>    admin@marketplace.com | senha: admin123');
        $this->line('<fg=green>🏪 VENDEDOR:</fg=green> vendedor@marketplace.com | senha: vendedor123');
        $this->line('<fg=blue>🛒 CLIENTE:</fg=blue>  cliente@marketplace.com | senha: cliente123');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('');
        $this->line('🔗 LINKS DE ACESSO:');
        $this->line('• Admin Panel: <fg=cyan>http://marketplace-b2c.test/admin/dashboard</fg=cyan>');
        $this->line('• Painel Vendedor: <fg=cyan>http://marketplace-b2c.test/seller/dashboard</fg=cyan>');
        $this->line('• Loja: <fg=cyan>http://marketplace-b2c.test/</fg=cyan>');
        $this->line('');
        
        $this->comment('💡 Dica: Use --fresh para recriar os usuários se necessário');
        $this->comment('   Exemplo: php artisan users:test --fresh');
    }
}
