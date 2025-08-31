<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Http\Controllers\TestImageUploadController;

echo "Testing Product Model:\n";
echo "Total products: " . Product::count() . "\n";

$products = Product::select('id', 'name', 'description', 'price')
    ->limit(5)
    ->get()
    ->map(function ($product) {
        $product->formatted_price = 'R$ ' . number_format($product->price, 2, ',', '.');
        return $product;
    });

echo "\nProducts found:\n";
foreach ($products as $product) {
    echo "ID: {$product->id} - Name: {$product->name} - Price: {$product->formatted_price}\n";
}

echo "\nTesting Controller:\n";
$controller = new TestImageUploadController();
try {
    $view = $controller->index();
    $data = $view->getData();
    echo "Controller returned " . count($data['products']) . " products\n";
    foreach ($data['products'] as $product) {
        echo "Product: {$product->name}\n";
    }
} catch (Exception $e) {
    echo "Error in controller: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}