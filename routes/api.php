<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\ProductImageController;

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/search', [SearchController::class, 'search'])->name('api.search');
});

// Rotas para upload de imagens de produtos (teste)
if (config('app.env') !== 'production') {
    Route::prefix('products/{product}')->group(function () {
        Route::get('/images', [ProductImageController::class, 'index'])->name('api.product.images.index');
        Route::post('/images', [ProductImageController::class, 'upload'])->name('api.product.images.upload');
        Route::post('/images/multiple', [ProductImageController::class, 'uploadMultiple'])->name('api.product.images.upload-multiple');
        Route::delete('/images/{media}', [ProductImageController::class, 'destroy'])->name('api.product.images.destroy');
        Route::post('/images/{media}/primary', [ProductImageController::class, 'setPrimary'])->name('api.product.images.set-primary');
        Route::post('/images/reorder', [ProductImageController::class, 'reorder'])->name('api.product.images.reorder');
    });
    
    Route::get('/storage/stats', [ProductImageController::class, 'getStorageStats'])->name('api.storage.stats');
    Route::post('/storage/cleanup', [ProductImageController::class, 'cleanup'])->name('api.storage.cleanup');
}