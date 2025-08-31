<?php
require_once 'bootstrap/app.php';

$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mainCategories = App\Models\Category::where('is_active', true)
    ->whereNull('parent_id')
    ->get(['name', 'slug', 'image_path']);

echo "=== CATEGORIAS PRINCIPAIS ===\n";
foreach ($mainCategories as $category) {
    echo "Nome: {$category->name}\n";
    echo "Slug: {$category->slug}\n";
    echo "Image Path: " . ($category->image_path ?? 'NULL') . "\n";
    if ($category->image_path) {
        $fullPath = public_path('storage/' . $category->image_path);
        echo "Arquivo existe: " . (file_exists($fullPath) ? 'SIM' : 'NÃO') . "\n";
    }
    echo "---\n";
}