<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

echo "=== CONFIGURANDO LINK SIMBÓLICO DO STORAGE ===\n";

$publicPath = public_path('storage');
$storagePath = storage_path('app/public');

echo "Caminho público: " . $publicPath . "\n";
echo "Caminho storage: " . $storagePath . "\n";

// Verificar se o diretório storage/app/public existe
if (!File::exists($storagePath)) {
    echo "Criando diretório storage/app/public...\n";
    File::makeDirectory($storagePath, 0755, true);
    echo "✅ Diretório criado!\n";
}

// Remover link existente se houver
if (File::exists($publicPath)) {
    echo "Removendo link existente...\n";
    if (is_link($publicPath)) {
        unlink($publicPath);
    } else {
        File::deleteDirectory($publicPath);
    }
    echo "✅ Link removido!\n";
}

// Criar o link simbólico
try {
    // No Windows, usar mklink
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = 'mklink /D "' . $publicPath . '" "' . $storagePath . '"';
        echo "Executando: " . $command . "\n";
        $output = shell_exec($command);
        echo "Resultado: " . $output . "\n";
    } else {
        // Unix/Linux
        symlink($storagePath, $publicPath);
    }
    
    echo "✅ Link simbólico criado com sucesso!\n";
    
    // Verificar se funcionou
    if (File::exists($publicPath)) {
        echo "✅ Verificação: Link está funcionando!\n";
    } else {
        echo "❌ Erro: Link não foi criado corretamente!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao criar link: " . $e->getMessage() . "\n";
    
    // Fallback: tentar usar Artisan
    echo "Tentando usar Artisan...\n";
    try {
        Artisan::call('storage:link');
        echo "✅ Link criado via Artisan!\n";
    } catch (Exception $e2) {
        echo "❌ Erro no Artisan: " . $e2->getMessage() . "\n";
    }
}

echo "\n=== CONFIGURAÇÃO CONCLUÍDA ===\n";