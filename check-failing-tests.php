<?php
echo "🚨 VERIFICAÇÃO URGENTE DE TESTS FALHANDO\n";
echo "=========================================\n\n";

// Carregar Laravel
try {
    require_once 'bootstrap/app.php';
    $app = \Illuminate\Foundation\Application::getInstance();
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo "✅ Laravel carregado com sucesso\n";
} catch (Exception $e) {
    echo "❌ ERRO ao carregar Laravel: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. Verificar banco de dados
echo "\n1. 🔍 VERIFICANDO BANCO DE DADOS:\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexão com banco: OK\n";
    
    // Listar tabelas
    $tables = DB::select("SHOW TABLES");
    echo "📊 Tabelas encontradas: " . count($tables) . "\n";
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (in_array($tableName, ['categories', 'users', 'products', 'seller_profiles'])) {
            $count = DB::table($tableName)->count();
            echo "   📋 {$tableName}: {$count} registros\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERRO no banco: " . $e->getMessage() . "\n";
}

// 2. Verificar Models principais
echo "\n2. 🏗️  VERIFICANDO MODELS:\n";
$models = [
    'Category' => 'App\Models\Category',
    'User' => 'App\Models\User', 
    'Product' => 'App\Models\Product',
    'SellerProfile' => 'App\Models\SellerProfile'
];

foreach ($models as $name => $class) {
    try {
        if (class_exists($class)) {
            $count = $class::count();
            echo "✅ {$name}: {$count} registros\n";
        } else {
            echo "❌ {$name}: Classe não existe!\n";
        }
    } catch (Exception $e) {
        echo "❌ {$name}: ERRO - " . $e->getMessage() . "\n";
    }
}

// 3. Verificar migrations
echo "\n3. 📝 VERIFICANDO MIGRATIONS:\n";
try {
    $migrations = DB::table('migrations')->count();
    echo "📊 Migrations executadas: {$migrations}\n";
    
    $lastMigration = DB::table('migrations')->orderBy('id', 'desc')->first();
    if ($lastMigration) {
        echo "🕐 Última migration: " . $lastMigration->migration . "\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO nas migrations: " . $e->getMessage() . "\n";
    echo "💡 Execute: php artisan migrate\n";
}

// 4. Verificar factories
echo "\n4. 🏭 VERIFICANDO FACTORIES:\n";
$factories = [
    'tests/database/factories/CategoryFactory.php',
    'database/factories/UserFactory.php',
    'database/factories/CategoryFactory.php'
];

foreach ($factories as $factory) {
    if (file_exists($factory)) {
        echo "✅ " . basename($factory) . ": Existe\n";
    } else {
        echo "❌ " . basename($factory) . ": NÃO existe\n";
    }
}

// 5. Executar teste básico
echo "\n5. 🧪 TESTE BÁSICO DE CATEGORIA:\n";
try {
    // Tentar criar uma categoria de teste
    $testCategory = new \App\Models\Category([
        'name' => 'Teste Urgente',
        'slug' => 'teste-urgente',
        'is_active' => true
    ]);
    
    echo "✅ Model Category pode ser instanciado\n";
    
    // Verificar fillable
    $fillable = $testCategory->getFillable();
    echo "📝 Campos fillable: " . implode(', ', $fillable) . "\n";
    
} catch (Exception $e) {
    echo "❌ ERRO no model Category: " . $e->getMessage() . "\n";
}

// 6. Verificar rotas principais
echo "\n6. 🛣️  VERIFICANDO ROTAS:\n";
try {
    $routes = ['/', '/admin/categories', '/categories:create-images'];
    
    foreach ($routes as $route) {
        if ($route === '/') {
            echo "✅ Rota home: definida\n";
        } else {
            echo "📋 {$route}: deve existir\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO nas rotas: " . $e->getMessage() . "\n";
}

echo "\n=========================================\n";
echo "🎯 PRÓXIMOS PASSOS:\n";
echo "1. Execute: php artisan migrate:fresh --seed\n";
echo "2. Execute: php artisan categories:create-images\n"; 
echo "3. Execute: php artisan test --stop-on-failure\n";
echo "4. Verifique: http://localhost/marketplace-b2c/public/\n";
echo "=========================================\n";
?>