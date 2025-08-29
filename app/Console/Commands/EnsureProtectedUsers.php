<?php
/**
 * Arquivo: app/Console/Commands/EnsureProtectedUsers.php
 * Descrição: Comando para garantir que usuários protegidos sempre existam
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Console\Commands;

use Database\Seeders\ProtectedUsersSeeder;
use Illuminate\Console\Command;

class EnsureProtectedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketplace:ensure-protected-users {--verify : Apenas verificar se existem}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Garante que os usuários protegidos do sistema sempre existam';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔒 VERIFICANDO USUÁRIOS PROTEGIDOS DO MARKETPLACE...');
        $this->newLine();

        if ($this->option('verify')) {
            return $this->verifyOnly();
        }

        // Executar seeder de usuários protegidos
        $this->info('Executando seeder de usuários protegidos...');
        
        $seeder = new ProtectedUsersSeeder();
        $seeder->run();
        
        $this->newLine();
        $this->success('✅ Usuários protegidos verificados/criados com sucesso!');
        
        // Mostrar credenciais
        $this->showCredentials();
        
        return 0;
    }

    /**
     * Apenas verifica se os usuários existem
     */
    private function verifyOnly(): int
    {
        $this->info('Verificando se todos os usuários protegidos existem...');
        
        if (ProtectedUsersSeeder::verifyProtectedUsers()) {
            $this->success('✅ Todos os usuários protegidos estão presentes!');
            $this->showCredentials();
            return 0;
        } else {
            $this->error('❌ Alguns usuários protegidos estão faltando!');
            $this->warn('Execute sem --verify para criá-los automaticamente.');
            return 1;
        }
    }

    /**
     * Mostra as credenciais dos usuários protegidos
     */
    private function showCredentials(): void
    {
        $this->newLine();
        $this->info('🔑 CREDENCIAIS DOS USUÁRIOS PROTEGIDOS:');
        $this->line(str_repeat('-', 60));
        
        $credentials = ProtectedUsersSeeder::getProtectedCredentials();
        
        foreach ($credentials as $cred) {
            $roleColor = match($cred['role']) {
                'admin' => 'red',
                'seller' => 'green', 
                'customer' => 'blue',
                default => 'white'
            };
            
            $this->line("<fg={$roleColor}>📧 {$cred['email']}</>");
            $this->line("   👤 {$cred['name']}");
            $this->line("   🔒 {$cred['password']}");
            $this->line("   🏷️  " . ucfirst($cred['role']));
            $this->newLine();
        }
        
        $this->info('🌐 URLs DE ACESSO:');
        $this->line('├── Home: https://marketplace-b2c.test/');
        $this->line('├── Admin: https://marketplace-b2c.test/admin/dashboard');
        $this->line('├── Seller: https://marketplace-b2c.test/seller/dashboard');
        $this->line('└── Login: https://marketplace-b2c.test/login');
    }

    /**
     * Método helper para success message
     */
    private function success(string $message): void
    {
        $this->line("<fg=green>{$message}</>");
    }
}