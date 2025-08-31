<?php

// Script para executar migrations manualmente
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Executando Migrations</h2>";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p>✅ Autoload carregado</p>";
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "<p>✅ App inicializado</p>";
    
    // Boot the application
    $app->boot();
    echo "<p>✅ App bootado</p>";
    
    // Verificar conexão com banco
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `marketplace-b2c`");
    echo "<p>✅ Banco 'marketplace-b2c' criado/verificado</p>";
    
    // Executar comando migrate
    echo "<p>🔄 Executando migrations...</p>";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true
    ]);
    
    echo "<p><strong>Código de saída:</strong> $exitCode</p>";
    echo "<h3>Output das Migrations:</h3>";
    echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    
    if ($exitCode === 0) {
        echo "<p style='color: green;'>✅ Migrations executadas com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao executar migrations (código: $exitCode)</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<h3>Stack trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><a href='check_tables.php'>Verificar tabelas criadas</a>";