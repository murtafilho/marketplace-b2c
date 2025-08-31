<?php
echo "<h1>🔍 DIAGNÓSTICO SIMPLES</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f0f0f0;}</style>";

echo "<h2>1. 📁 Estrutura de Arquivos:</h2>";
$files = [
    '../vendor/autoload.php' => 'Composer Autoload',
    '../bootstrap/app.php' => 'Laravel Bootstrap', 
    '../app/Models/Category.php' => 'Model Category',
    '../.env' => 'Environment Config'
];

foreach ($files as $path => $name) {
    if (file_exists($path)) {
        echo "<p style='color:green'>✅ {$name}: EXISTE</p>";
    } else {
        echo "<p style='color:red'>❌ {$name}: NÃO EXISTE</p>";
    }
}

echo "<h2>2. 🧪 Test PHP Básico:</h2>";
try {
    if (file_exists('../vendor/autoload.php')) {
        require_once '../vendor/autoload.php';
        echo "<p style='color:green'>✅ Autoload carregado</p>";
        
        if (class_exists('Illuminate\Foundation\Application')) {
            echo "<p style='color:green'>✅ Laravel Application encontrado</p>";
        } else {
            echo "<p style='color:red'>❌ Laravel Application NÃO encontrado</p>";
        }
        
    } else {
        echo "<p style='color:red'>❌ vendor/autoload.php não existe</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>3. 🔧 Soluções:</h2>";
echo "<ol>";
echo "<li>Execute: <code>composer install</code></li>";
echo "<li>Se falhar: <code>composer update</code></li>";
echo "<li>Verifique se o PHP está na versão 8.3+</li>";
echo "<li>Reinicie o servidor local</li>";
echo "</ol>";

echo "<h2>4. 📋 Informações do Sistema:</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>File Exists vendor:</strong> " . (is_dir('../vendor') ? 'SIM' : 'NÃO') . "</p>";
?>