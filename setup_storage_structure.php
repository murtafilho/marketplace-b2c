<?php
/**
 * Script para configurar estrutura de diretórios para storage local
 * Execute: php setup_storage_structure.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Configuração da Estrutura de Storage ===\n\n";

// Diretórios necessários
$directories = [
    storage_path('app/public'),
    storage_path('app/public/products'),
    storage_path('app/public/temp'),
    storage_path('app/public/media-library'),
    storage_path('app/public/media-library/temp'),
    public_path('storage')
];

echo "1. Criando diretórios necessários...\n";
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "   ✓ Criado: {$dir}\n";
        } else {
            echo "   ✗ Erro ao criar: {$dir}\n";
        }
    } else {
        echo "   ✓ Já existe: {$dir}\n";
    }
}

echo "\n2. Verificando link simbólico...\n";
$linkPath = public_path('storage');
$targetPath = storage_path('app/public');

if (is_link($linkPath)) {
    echo "   ✓ Link simbólico já existe\n";
} else {
    // Remover se existir como diretório
    if (is_dir($linkPath)) {
        rmdir($linkPath);
    }
    
    // Criar link simbólico
    if (symlink($targetPath, $linkPath)) {
        echo "   ✓ Link simbólico criado com sucesso\n";
    } else {
        echo "   ✗ Erro ao criar link simbólico\n";
        echo "   Execute manualmente: php artisan storage:link\n";
    }
}

echo "\n3. Configurando permissões...\n";
$storageDirectories = [
    storage_path('app'),
    storage_path('app/public'),
    storage_path('logs')
];

foreach ($storageDirectories as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "   ✓ Permissões configuradas para: {$dir}\n";
    }
}

echo "\n4. Testando escrita nos diretórios...\n";
$testDirectories = [
    storage_path('app/public/products'),
    storage_path('app/public/temp')
];

foreach ($testDirectories as $dir) {
    $testFile = $dir . '/test_write.txt';
    if (file_put_contents($testFile, 'test') !== false) {
        unlink($testFile);
        echo "   ✓ Escrita OK: {$dir}\n";
    } else {
        echo "   ✗ Erro de escrita: {$dir}\n";
    }
}

echo "\n5. Verificando configuração do .env...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Verificar FILESYSTEM_DISK
    if (strpos($envContent, 'FILESYSTEM_DISK=public') !== false) {
        echo "   ✓ FILESYSTEM_DISK configurado como 'public'\n";
    } else {
        echo "   ⚠ FILESYSTEM_DISK não está configurado como 'public'\n";
        echo "   Adicione: FILESYSTEM_DISK=public\n";
    }
    
    // Verificar APP_URL
    if (preg_match('/APP_URL=(.+)/', $envContent, $matches)) {
        $appUrl = trim($matches[1]);
        echo "   ✓ APP_URL: {$appUrl}\n";
    } else {
        echo "   ⚠ APP_URL não configurado\n";
    }
} else {
    echo "   ✗ Arquivo .env não encontrado\n";
}

echo "\n6. Testando URLs de acesso...\n";
$testImagePath = storage_path('app/public/test_image.jpg');

// Criar uma imagem de teste simples (1x1 pixel)
$imageData = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/wA==');
file_put_contents($testImagePath, $imageData);

if (file_exists($testImagePath)) {
    $publicUrl = url('storage/test_image.jpg');
    echo "   ✓ Imagem de teste criada\n";
    echo "   URL de acesso: {$publicUrl}\n";
    
    // Limpar arquivo de teste
    unlink($testImagePath);
} else {
    echo "   ✗ Erro ao criar imagem de teste\n";
}

echo "\n7. Verificando dependências do Spatie Media Library...\n";
try {
    $mediaModel = new \Spatie\MediaLibrary\MediaCollections\Models\Media();
    echo "   ✓ Spatie Media Library carregado\n";
} catch (Exception $e) {
    echo "   ✗ Erro ao carregar Spatie Media Library: " . $e->getMessage() . "\n";
}

echo "\n8. Verificando Intervention Image...\n";
try {
    if (class_exists('Intervention\\Image\\Facades\\Image')) {
        echo "   ✓ Intervention Image disponível\n";
    } else {
        echo "   ✗ Intervention Image não encontrado\n";
    }
} catch (Exception $e) {
    echo "   ✗ Erro ao verificar Intervention Image: " . $e->getMessage() . "\n";
}

echo "\n=== Resumo da Configuração ===\n";
echo "Storage Path: " . storage_path('app/public') . "\n";
echo "Public Path: " . public_path('storage') . "\n";
echo "Base URL: " . url('storage') . "\n";

echo "\n=== Próximos Passos ===\n";
echo "1. Execute: php artisan migrate (se necessário)\n";
echo "2. Acesse: " . url('/test/image-upload') . "\n";
echo "3. Teste o upload de imagens\n";

echo "\n✅ Configuração concluída!\n";