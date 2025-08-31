<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Produtos na base de dados: " . Product::count() . "\n";

if (Product::count() > 0) {
    $product = Product::first();
    echo "Primeiro produto: ID {$product->id} - {$product->name}\n";
} else {
    echo "Nenhum produto encontrado na base de dados.\n";
}