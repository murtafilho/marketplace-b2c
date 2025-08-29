<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = new Application(realpath(__DIR__));

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Factory Updates...\n";
echo str_repeat("=", 50) . "\n";

try {
    // Test Category Factory
    echo "\n📂 Testing Category Factory:\n";
    $category = App\Models\Category::factory()->make();
    echo "✅ Category: {$category->name}\n";
    echo "✅ Icon: {$category->icon}\n";
    
    // Test SellerProfile Factory  
    echo "\n👤 Testing SellerProfile Factory:\n";
    $sellerProfile = App\Models\SellerProfile::factory()->make(['user_id' => 1]);
    echo "✅ Company: {$sellerProfile->company_name}\n";
    echo "✅ Bank: {$sellerProfile->bank_name}\n";
    echo "✅ Bank Agency: {$sellerProfile->bank_agency}\n";
    
    // Test Product Factory
    echo "\n📦 Testing Product Factory:\n";
    $product = App\Models\Product::factory()->make([
        'seller_id' => 1,
        'category_id' => 1
    ]);
    echo "✅ Product: {$product->name}\n";
    echo "✅ Brand: " . ($product->brand ?? 'N/A') . "\n";
    echo "✅ Model: " . ($product->model ?? 'N/A') . "\n";
    echo "✅ Warranty: " . ($product->warranty_months ?? 'N/A') . " months\n";
    echo "✅ Shipping Class: " . ($product->shipping_class ?? 'N/A') . "\n";
    
    echo "\n🎉 All factories working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error testing factories: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}