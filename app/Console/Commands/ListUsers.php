<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    protected $signature = 'users:list';
    protected $description = 'List all users in the database';

    public function handle()
    {
        $this->info('=== USUÁRIOS NO SISTEMA ===');
        
        $users = User::all();
        
        if ($users->count() == 0) {
            $this->warn('Nenhum usuário encontrado.');
            return;
        }
        
        foreach ($users as $user) {
            $this->line("{$user->id} - {$user->name} ({$user->email}) - {$user->role}");
        }
        
        $this->info("\nTotal: " . $users->count() . " usuário(s)");
    }
}