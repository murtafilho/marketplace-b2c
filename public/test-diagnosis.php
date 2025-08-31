<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚨 DIAGNÓSTICO DE TESTS VIA BROWSER</h1>";
echo "<style>body{font-family:monospace;background:#f5f5f5;padding:20px;} .error{color:red;} .success{color:green;} .warning{color:orange;}</style>";

try {
    // Tentar carregar Laravel
    require_once '../bootstrap/app.php';
    $app = \Illuminate\Foundation\Application::getInstance();
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "<p class='success'>✅ Laravel carregado com sucesso</p>";
    
    // 1. Verificar banco
    echo "<h2>1. 🔍 BANCO DE DADOS:</h2>";
    try {
        $pdo = DB::connection()->getPdo();
        echo "<p class='success'>✅ Conexão OK</p>";
        
        // Contar registros principais
        $categories = App\Models\Category::count();
        $users = App\Models\User::count();
        
        echo "<p>📊 Categorias: <strong>{$categories}</strong></p>";
        echo "<p>👥 Usuários: <strong>{$users}</strong></p>";
        
        if ($categories === 0) {
            echo "<p class='error'>❌ NENHUMA CATEGORIA! Execute: php artisan db:seed</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erro no banco: " . $e->getMessage() . "</p>";
    }
    
    // 2. Verificar categorias principais
    echo "<h2>2. 🏠 CATEGORIAS PRINCIPAIS:</h2>";
    try {
        $mainCategories = App\Models\Category::whereNull('parent_id')->get();
        echo "<p>📊 Total principais: <strong>" . $mainCategories->count() . "</strong></p>";
        
        if ($mainCategories->count() === 0) {
            echo "<p class='error'>❌ Nenhuma categoria principal - por isso não aparecem na homepage!</p>";
        } else {
            echo "<ul>";
            foreach ($mainCategories->take(5) as $cat) {
                $img = $cat->image_path ? '✅' : '❌';
                echo "<li>{$img} {$cat->name} (slug: {$cat->slug})</li>";
            }
            echo "</ul>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erro: " . $e->getMessage() . "</p>";
    }
    
    // 3. Simular test básico
    echo "<h2>3. 🧪 TEST BÁSICO:</h2>";
    try {
        // Tentar criar categoria de teste
        $testCat = new App\Models\Category([
            'name' => 'Test Browser',
            'slug' => 'test-browser',
            'is_active' => true
        ]);
        
        echo "<p class='success'>✅ Model Category funciona</p>";
        echo "<p>📝 Fillable: " . implode(', ', $testCat->getFillable()) . "</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erro no model: " . $e->getMessage() . "</p>";
    }
    
    // 4. Verificar arquivos
    echo "<h2>4. 📁 ARQUIVOS IMPORTANTES:</h2>";
    $files = [
        '../app/Models/Category.php',
        '../resources/views/home.blade.php',
        '../resources/views/components/category-grid.blade.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ " . basename($file) . "</p>";
        } else {
            echo "<p class='error'>❌ " . basename($file) . " - NÃO EXISTE</p>";
        }
    }
    
    echo "<h2>💡 PRÓXIMOS PASSOS:</h2>";
    echo "<ol>";
    echo "<li>Execute: <code>php artisan db:seed</code></li>";
    echo "<li>Execute: <code>php artisan categories:create-images</code></li>";
    echo "<li>Execute: <code>php artisan test</code></li>";
    echo "<li>Teste: <a href='/'>Homepage</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ ERRO CRÍTICO: " . $e->getMessage() . "</p>";
    echo "<p>🔧 Verifique se o Laravel está configurado corretamente</p>";
}
?>