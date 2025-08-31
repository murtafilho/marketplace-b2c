<?php
require_once '../bootstrap/app.php';
$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>🔍 Debug Homepage Categories</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f0f0f0;} .category{margin:10px;padding:15px;background:white;border-radius:8px;} img{width:64px;height:64px;object-fit:cover;border-radius:8px;}</style>";

// Simular exatamente o que o HomeController faz
$mainCategories = App\Models\Category::where('is_active', true)
    ->whereNull('parent_id')
    ->with(['children' => function($query) {
        $query->withCount(['products' => function($subQuery) {
            $subQuery->where('status', 'active');
        }]);
    }])
    ->orderBy('sort_order')
    ->get();

echo "<h2>📊 Resultado da Query do HomeController:</h2>";
echo "<p><strong>Total de categorias principais:</strong> " . $mainCategories->count() . "</p>";

if ($mainCategories->count() === 0) {
    echo "<p style='color:red;'>❌ Nenhuma categoria principal encontrada!</p>";
    echo "<p>Categorias com parent_id null: " . App\Models\Category::whereNull('parent_id')->count() . "</p>";
    echo "<p>Categorias ativas: " . App\Models\Category::where('is_active', true)->count() . "</p>";
} else {
    echo "<h2>🖼️ Categorias e suas imagens:</h2>";
    
    foreach ($mainCategories->take(6) as $category) {
        echo "<div class='category'>";
        echo "<h3>{$category->name}</h3>";
        echo "<p>Slug: {$category->slug}</p>";
        echo "<p>Image Path: " . ($category->image_path ?? 'NULL') . "</p>";
        
        if ($category->image_path) {
            $url = asset('storage/' . $category->image_path);
            echo "<p>URL: <a href='{$url}' target='_blank'>{$url}</a></p>";
            echo "<img src='{$url}' alt='{$category->name}' onerror='this.style.border=\"2px solid red\";'>";
            
            // Verificar se arquivo existe
            $filePath = public_path('storage/' . $category->image_path);
            if (file_exists($filePath)) {
                echo "<p style='color:green;'>✅ Arquivo existe no servidor</p>";
            } else {
                echo "<p style='color:red;'>❌ Arquivo NÃO existe: {$filePath}</p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠️ Sem imagem definida</p>";
        }
        
        echo "</div>";
    }
}

echo "<h2>🔧 URLs de Teste:</h2>";
echo "<ul>";
$testUrls = [
    asset('storage/categories/eletronicos.jpg'),
    asset('storage/categories/roupas-acessorios.jpg'),
    asset('storage/categories/casa-jardim.jpg')
];

foreach ($testUrls as $url) {
    echo "<li><a href='{$url}' target='_blank'>{$url}</a></li>";
}
echo "</ul>";
?>