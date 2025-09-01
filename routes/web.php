<?php
/**
 * Arquivo: routes/web.php
 * Descrição: Rotas web do marketplace B2C
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller\OnboardingController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\Admin\SellerManagementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Auth\QuickLoginController;
use App\Http\Controllers\Auth\SellerRegistrationController;
use App\Http\Controllers\Webhooks\MercadoPagoWebhookController;
use App\Http\Controllers\TestUploadController;
use Illuminate\Support\Facades\Route;

// Página inicial do marketplace
Route::get('/', [HomeController::class, 'index'])->name('home');

// Busca de produtos
Route::get('/buscar', [HomeController::class, 'search'])->name('search');

// Produtos por categoria
Route::get('/categoria/{slug}', [HomeController::class, 'category'])->name('category.show');

// Página de produto individual
Route::get('/produto/{id}', [HomeController::class, 'product'])->name('product.show');

// Rotas de E-commerce - Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'store'])->name('add');
    Route::put('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'destroy'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// Rotas de E-commerce - Checkout
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    Route::get('/pending', [CheckoutController::class, 'pending'])->name('pending');
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
});

// Webhooks do MercadoPago (sem middleware para permitir acesso externo)
Route::prefix('webhooks/mercadopago')->name('webhooks.mercadopago.')->group(function () {
    Route::post('/payment', [MercadoPagoWebhookController::class, 'payment'])->name('payment');
    Route::post('/merchant_order', [MercadoPagoWebhookController::class, 'merchantOrder'])->name('merchant_order');
});

// Rotas de E-commerce - Products
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/category/{category}', [ProductController::class, 'category'])->name('category');
});

// Rota para listagem de categorias
Route::get('/categorias', function () {
    return view('categories.index');
})->name('categories.index');

// Rota de teste mobile-first (development)
Route::get('/test-mobile', function () {
    return view('test-mobile');
})->name('test.mobile');

// Rotas de login rápido para desenvolvimento/teste
Route::get('/quick-login', [QuickLoginController::class, 'quickLogin'])->name('quick.login');
Route::post('/force-logout', [QuickLoginController::class, 'forceLogout'])->name('force.logout');

// Rota de cadastro unificado (usuário + loja)
Route::get('/criar-loja', [SellerRegistrationController::class, 'create'])->name('seller.register');
Route::post('/criar-loja', [SellerRegistrationController::class, 'store'])->name('seller.register.store');

// Dashboard com redirecionamento baseado no role
Route::get('/dashboard', function () {
    $user = request()->user();
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'seller') {
        return redirect()->route('seller.dashboard');
    } else {
        // Customer - redirecionar para home ou perfil
        return redirect()->route('home');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rota para criar perfil de vendedor (usuários logados)
Route::post('/become-seller', [SellerDashboardController::class, 'becomeSeller'])
    ->middleware('auth')
    ->name('become-seller');

// Seller Routes
Route::prefix('seller')->name('seller.')->middleware(['auth', 'seller'])->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
    
    // Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/pending', [OnboardingController::class, 'pending'])->name('pending');
    
    // Products
    Route::resource('products', SellerProductController::class);
    Route::patch('/products/{product}/toggle-status', [SellerProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('/products/{product}/duplicate', [SellerProductController::class, 'duplicate'])->name('products.duplicate');
    Route::post('/products/{product}/images', [SellerProductController::class, 'uploadImages'])->name('products.upload-images');
    Route::delete('/products/images/{image}', [SellerProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::patch('/products/{product}/inventory', [SellerProductController::class, 'updateInventory'])->name('products.update-inventory');
    Route::patch('/products/bulk-update', [SellerProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    
    // Profile
    Route::get('/profile', [SellerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [SellerProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/banking', [SellerProfileController::class, 'updateBankAccount'])->name('profile.banking');
    Route::put('/profile/notifications', [SellerProfileController::class, 'updateNotifications'])->name('profile.notifications');
    Route::put('/profile/seo', [SellerProfileController::class, 'updateSeo'])->name('profile.seo');
    Route::delete('/profile/deactivate', [SellerProfileController::class, 'deactivate'])->name('profile.deactivate');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/sellers', [SellerManagementController::class, 'index'])->name('sellers.index');
    Route::get('/sellers/{seller}', [SellerManagementController::class, 'show'])->name('sellers.show');
    Route::post('/sellers/{seller}/approve', [SellerManagementController::class, 'approve'])->name('sellers.approve');
    Route::post('/sellers/{seller}/reject', [SellerManagementController::class, 'reject'])->name('sellers.reject');
    Route::post('/sellers/{seller}/suspend', [SellerManagementController::class, 'suspend'])->name('sellers.suspend');
    Route::post('/sellers/{seller}/commission', [SellerManagementController::class, 'updateCommission'])->name('sellers.commission');
    
    // Rotas para categorias
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // Relatórios
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/financial', [ReportsController::class, 'financial'])->name('financial');
        Route::get('/sellers', [ReportsController::class, 'sellers'])->name('sellers');
        Route::get('/products', [ReportsController::class, 'products'])->name('products');
        Route::get('/export', [ReportsController::class, 'export'])->name('export');
    });
});


// Rotas de teste (apenas em desenvolvimento)
if (config('app.env') !== 'production') {
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/create-products', function() {
            require_once base_path('create_test_products.php');
        });
    });
}

require __DIR__.'/auth.php';
