<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\User;

// Verificar se existe usuário
$user = User::first();
if (!$user) {
    echo "Criando usuário de teste...\n";
    $user = User::create([
        'name' => 'Usuário Teste',
        'email' => 'teste@teste.com',
        'password' => bcrypt('123456'),
        'role' => 'seller'
    ]);
}

// Criar categoria de teste se não existir
$category = Category::firstOrCreate(
    ['name' => 'Eletrônicos'],
    [
        'slug' => 'eletronicos',
        'description' => 'Categoria de teste para eletrônicos',
        'status' => 'active'
    ]
);

// Criar seller de teste se não existir
$seller = SellerProfile::first();
if (!$seller) {
    echo "Criando seller de teste...\n";
    $seller = SellerProfile::create([
        'user_id' => $user->id,
        'business_name' => 'Loja de Teste',
        'business_type' => 'individual',
        'document_number' => '12345678901',
        'phone' => '11999999999',
        'status' => 'approved'
    ]);
}

// Criar produtos de teste
$products = [
    [
        'name' => 'Smartphone Samsung Galaxy',
        'description' => 'Smartphone com tela de 6.1 polegadas e câmera de 64MP',
        'price' => 1299.99,
        'sku' => 'SAMSUNG-001'
    ],
    [
        'name' => 'Notebook Dell Inspiron',
        'description' => 'Notebook com processador Intel i5 e 8GB de RAM',
        'price' => 2499.99,
        'sku' => 'DELL-001'
    ],
    [
        'name' => 'Fone de Ouvido Bluetooth',
        'description' => 'Fone sem fio com cancelamento de ruído',
        'price' => 299.99,
        'sku' => 'FONE-001'
    ]
];

foreach ($products as $productData) {
    $product = Product::firstOrCreate(
        ['sku' => $productData['sku']],
        [
            'seller_id' => $seller->id,
            'category_id' => $category->id,
            'name' => $productData['name'],
            'slug' => \Illuminate\Support\Str::slug($productData['name']),
            'description' => $productData['description'],
            'price' => $productData['price'],
            'stock_quantity' => 10,
            'stock_status' => 'in_stock',
            'status' => 'active'
        ]
    );
    
    echo "Produto: {$product->name} (ID: {$product->id})\n";
}

echo "\nTotal de produtos: " . Product::count() . "\n";