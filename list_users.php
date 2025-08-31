<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User;

echo "=== USUÁRIOS NO SISTEMA ===\n";

$users = User::all();

if ($users->count() == 0) {
    echo "Nenhum usuário encontrado.\n";
} else {
    foreach ($users as $user) {
        echo "{$user->id} - {$user->name} ({$user->email}) - {$user->role}\n";
    }
}

echo "\nTotal: " . $users->count() . " usuário(s)\n";