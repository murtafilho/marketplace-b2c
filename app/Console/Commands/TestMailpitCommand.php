<?php
/**
 * Arquivo: app/Console/Commands/TestMailpitCommand.php
 * Descrição: Comando para testar configuração do Mailpit
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailpitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailpit:test {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o envio de email através do Mailpit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'teste@exemplo.com';
        
        $this->info('🔧 Testando configuração do Mailpit...');
        $this->newLine();
        
        // Mostrar configurações atuais
        $this->table(
            ['Configuração', 'Valor'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption') ?? 'null'],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );
        
        $this->newLine();
        $this->info('📧 Enviando email de teste para: ' . $email);
        
        try {
            Mail::raw('Este é um email de teste do Marketplace B2C!

Se você está vendo esta mensagem no Mailpit, significa que a configuração está funcionando corretamente.

Informações do teste:
- Data/Hora: ' . now()->format('d/m/Y H:i:s') . '
- Servidor: Mailpit (Laragon)
- Aplicação: ' . config('app.name') . '
- Ambiente: ' . config('app.env'), function ($message) use ($email) {
                $message->to($email)
                    ->subject('🎉 Teste do Mailpit - ' . config('app.name'));
            });
            
            $this->newLine();
            $this->info('✅ Email enviado com sucesso!');
            $this->newLine();
            $this->info('📌 Verifique o email em: http://localhost:8025');
            $this->info('   (Interface web do Mailpit)');
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Erro ao enviar email:');
            $this->error($e->getMessage());
            $this->newLine();
            $this->warn('Possíveis soluções:');
            $this->line('1. Verifique se o Mailpit está rodando no Laragon');
            $this->line('2. No Laragon, vá em Menu > Mailpit > Start');
            $this->line('3. Verifique se a porta 1025 não está bloqueada');
            $this->line('4. Execute: php artisan config:clear');
        }
        
        return Command::SUCCESS;
    }
}
