<?php
/**
 * Arquivo: config/marketplace.php
 * Descrição: Configurações do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

return [
    'default_commission' => env('MARKETPLACE_COMMISSION', 10.0),
    'seller_auto_approve' => env('SELLER_AUTO_APPROVE', false),
    'product_auto_approve' => env('PRODUCT_AUTO_APPROVE', false),
    'max_product_images' => 5,
    'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif'],
    'max_file_size' => 2048, // KB
];