<?php
/**
 * Script executado automaticamente após migrations
 * Garante que usuários protegidos sempre existam
 */

// Só executar se estivermos no contexto do Laravel
if (defined('LARAVEL_START') || isset($app)) {
    try {
        // Verificar se precisa executar
        $needsUsers = false;
        
        if (function_exists('app') && app()->bound('db')) {
            $userCount = \App\Models\User::whereIn('role', ['admin', 'seller', 'customer'])->count();
            $needsUsers = $userCount < 3;
        }
        
        if ($needsUsers) {
            echo "🔄 Executando auto-restore de usuários protegidos...\n";
            
            $seeder = new \Database\Seeders\ProtectedUsersSeeder();
            $seeder->run();
            
            echo "✅ Usuários protegidos restaurados automaticamente!\n";
        }
    } catch (\Exception $e) {
        echo "⚠️ Auto-restore falhou silenciosamente: " . $e->getMessage() . "\n";
    }
}