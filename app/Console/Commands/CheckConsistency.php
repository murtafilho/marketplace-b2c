<?php

/**
 * Arquivo: app/Console/Commands/CheckConsistency.php
 * Descrição: Script para verificar consistência - Parte 1
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckConsistency extends Command
{
    protected $signature = 'app:check-consistency 
                            {--check=all : all|database|migrations|controllers|routes|models|views}
                            {--dictionary : Gerar dicionário de dados}
                            {--fix : Tentar corrigir problemas automaticamente}
                            {--export : Exportar relatório para arquivo}';

    protected $description = 'Verifica consistência completa do projeto Laravel e gera dicionário de dados';

    protected $errors = [];
    protected $warnings = [];
    protected $info = [];
    protected $success = [];
    protected $dataDictionary = [];

    protected $expectedTables = [
        'users' => ['description' => 'Tabela principal de usuários', 'has_soft_delete' => true, 'has_timestamps' => true],
        'seller_profiles' => ['description' => 'Perfis detalhados dos vendedores', 'has_soft_delete' => false, 'has_timestamps' => true],
        'products' => ['description' => 'Catálogo de produtos', 'has_soft_delete' => true, 'has_timestamps' => true],
        'product_images' => ['description' => 'Imagens dos produtos', 'has_soft_delete' => false, 'has_timestamps' => true],
        'product_variations' => ['description' => 'Variações de produtos', 'has_soft_delete' => false, 'has_timestamps' => true],
        'categories' => ['description' => 'Categorias e subcategorias', 'has_soft_delete' => false, 'has_timestamps' => true],
        'carts' => ['description' => 'Carrinhos de compra', 'has_soft_delete' => false, 'has_timestamps' => true],
        'cart_items' => ['description' => 'Itens do carrinho', 'has_soft_delete' => false, 'has_timestamps' => true],
        'orders' => ['description' => 'Pedidos principais', 'has_soft_delete' => true, 'has_timestamps' => true],
        'order_items' => ['description' => 'Itens dos pedidos', 'has_soft_delete' => false, 'has_timestamps' => true],
        'sub_orders' => ['description' => 'Pedidos por vendedor', 'has_soft_delete' => false, 'has_timestamps' => true],
        'transactions' => ['description' => 'Transações e splits', 'has_soft_delete' => false, 'has_timestamps' => true],
        'seller_shipping_options' => ['description' => 'Opções de envio', 'has_soft_delete' => false, 'has_timestamps' => true]
    ];

    protected $expectedControllers = [
        'Admin/DashboardController' => ['status' => 'implemented'],
        'Admin/SellerManagementController' => ['status' => 'implemented'],
        'Admin/CommissionController' => ['status' => 'pending'],
        'Seller/DashboardController' => ['status' => 'implemented'],
        'Seller/OnboardingController' => ['status' => 'implemented'],
        'Seller/ProductController' => ['status' => 'implemented'],
        'Seller/OrderController' => ['status' => 'pending'],
        'Shop/HomeController' => ['status' => 'implemented'],
        'Shop/ProductController' => ['status' => 'implemented'],
        'Shop/CartController' => ['status' => 'implemented'],
        'Shop/CheckoutController' => ['status' => 'implemented']
    ];

    protected $expectedModels = [
        'User',
        'SellerProfile',
        'Product',
        'ProductImage',
        'ProductVariation',
        'Category',
        'Cart',
        'CartItem',
        'Order',
        'OrderItem',
        'SubOrder',
        'Transaction'
    ];

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('   VERIFICADOR DE CONSISTÊNCIA - MARKETPLACE B2C v1.0');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        $checkType = $this->option('check');

        if ($checkType === 'all' || $checkType === 'database') {
            $this->checkDatabase();
        }

        if ($checkType === 'all' || $checkType === 'migrations') {
            $this->checkMigrations();
        }

        if ($checkType === 'all' || $checkType === 'models') {
            $this->checkModels();
        }

        if ($checkType === 'all' || $checkType === 'controllers') {
            $this->checkControllers();
        }

        if ($checkType === 'all' || $checkType === 'routes') {
            $this->checkRoutes();
        }

        if ($checkType === 'all' || $checkType === 'views') {
            $this->checkViews();
        }
        
        if ($checkType === 'all') {
            $this->checkMiddlewares();
        }

        if ($this->option('dictionary')) {
            $this->generateDataDictionary();
        }

        $this->displaySummary();

        if ($this->option('export')) {
            $this->exportReport();
        }

        return Command::SUCCESS;
    }

    protected function checkDatabase()
    {
        $this->info('📊 VERIFICANDO BANCO DE DADOS...');
        $this->newLine();

        try {
            DB::connection()->getPdo();
            $this->success[] = '✓ Conexão com banco de dados estabelecida';

            $tables = collect(DB::select('SHOW TABLES'))->map(function ($table) {
                $array = (array) $table;
                return array_values($array)[0];
            })->toArray();

            foreach ($this->expectedTables as $table => $config) {
                if (in_array($table, $tables)) {
                    $this->success[] = "✓ Tabela '{$table}' existe";
                    $this->checkTableStructure($table, $config);
                } else {
                    $this->errors[] = "✗ Tabela '{$table}' NÃO ENCONTRADA";
                }
            }

            $extraTables = array_diff(
                $tables,
                array_keys($this->expectedTables),
                [
                    'migrations',
                    'password_reset_tokens',
                    'sessions',
                    'cache',
                    'cache_locks',
                    'jobs',
                    'job_batches',
                    'failed_jobs'
                ]
            );

            foreach ($extraTables as $table) {
                $this->warnings[] = "⚠ Tabela extra encontrada: '{$table}'";
            }
        } catch (\Exception $e) {
            $this->errors[] = "✗ Erro ao conectar com banco: " . $e->getMessage();
        }
    }

    protected function checkTableStructure($table, $config)
    {
        $columns = Schema::getColumnListing($table);

        if ($config['has_soft_delete'] && !in_array('deleted_at', $columns)) {
            $this->warnings[] = "⚠ Tabela '{$table}' deveria ter soft delete";
        }

        if ($config['has_timestamps']) {
            if (!in_array('created_at', $columns)) {
                $this->warnings[] = "⚠ Tabela '{$table}' sem created_at";
            }
            if (!in_array('updated_at', $columns)) {
                $this->warnings[] = "⚠ Tabela '{$table}' sem updated_at";
            }
        }

        $this->dataDictionary[$table] = [
            'description' => $config['description'],
            'columns' => []
        ];

        $columnDetails = DB::select("SHOW FULL COLUMNS FROM `{$table}`");
        foreach ($columnDetails as $column) {
            $this->dataDictionary[$table]['columns'][$column->Field] = [
                'type' => $column->Type,
                'null' => $column->Null === 'YES',
                'key' => $column->Key,
                'default' => $column->Default,
                'extra' => $column->Extra,
                'comment' => $column->Comment
            ];
        }
    }

    protected function checkMigrations()
    {
        $this->info('📁 VERIFICANDO MIGRATIONS...');
        $this->newLine();

        $migrationPath = database_path('migrations');
        $migrations = File::files($migrationPath);

        $this->info[] = "→ Total de migrations: " . count($migrations);

        $executed = DB::table('migrations')->pluck('migration')->toArray();
        $pending = [];

        foreach ($migrations as $migration) {
            $filename = $migration->getFilename();
            $migrationName = str_replace('.php', '', $filename);

            if (!in_array($migrationName, $executed)) {
                $pending[] = $migrationName;
                $this->warnings[] = "⚠ Migration pendente: {$migrationName}";
            }
        }

        if (empty($pending)) {
            $this->success[] = "✓ Todas as migrations executadas";
        } else {
            $this->errors[] = "✗ " . count($pending) . " migrations pendentes";
        }
    }


    protected function checkModels()
    {
        $this->info('🗂️ VERIFICANDO MODELS...');
        $this->newLine();

        $modelPath = app_path('Models');

        foreach ($this->expectedModels as $model) {
            $modelFile = $modelPath . '/' . $model . '.php';

            if (File::exists($modelFile)) {
                $this->success[] = "✓ Model '{$model}' encontrado";
                $this->checkModelRelationships($model, $modelFile);
            } else {
                $this->errors[] = "✗ Model '{$model}' NÃO ENCONTRADO";
            }
        }
    }

    protected function checkModelRelationships($model, $modelFile)
    {
        $content = File::get($modelFile);

        $expectedRelations = [
            'User' => ['sellerProfile', 'orders', 'carts'],
            'SellerProfile' => ['user', 'products', 'subOrders', 'transactions'],
            'Product' => ['seller', 'category', 'images', 'variations', 'cartItems', 'orderItems'],
            'ProductImage' => ['product'],
            'ProductVariation' => ['product', 'cartItems', 'orderItems'],
            'Category' => ['products', 'parent', 'children'],
            'Cart' => ['user', 'items'],
            'CartItem' => ['cart', 'product', 'variation'],
            'Order' => ['user', 'items', 'subOrders', 'transactions'],
            'OrderItem' => ['order', 'subOrder', 'product', 'variation'],
            'SubOrder' => ['order', 'seller', 'items'],
            'Transaction' => ['order', 'seller']
        ];

        if (isset($expectedRelations[$model])) {
            foreach ($expectedRelations[$model] as $relation) {
                if (!str_contains($content, "function {$relation}(")) {
                    $this->warnings[] = "⚠ Model '{$model}' pode estar sem relacionamento '{$relation}'";
                }
            }
        }

        // Verificar fillable/guarded
        if (!str_contains($content, '$fillable') && !str_contains($content, '$guarded')) {
            $this->warnings[] = "⚠ Model '{$model}' sem definição de fillable/guarded";
        }

        // Verificar casts
        if ($model === 'Order' || $model === 'Product') {
            if (!str_contains($content, '$casts')) {
                $this->info[] = "→ Model '{$model}' pode precisar de casts para campos JSON";
            }
        }
    }

    protected function checkControllers()
    {
        $this->info('🎮 VERIFICANDO CONTROLLERS...');
        $this->newLine();

        $controllerPath = app_path('Http/Controllers');

        foreach ($this->expectedControllers as $controller => $config) {
            $controllerFile = $controllerPath . '/' . $controller . '.php';

            if (File::exists($controllerFile)) {
                if ($config['status'] === 'implemented') {
                    $this->success[] = "✓ Controller '{$controller}' implementado";
                } else {
                    $this->info[] = "→ Controller '{$controller}' existe (status: {$config['status']})";
                }

                $this->checkControllerMethods($controller, $controllerFile);
            } else {
                if ($config['status'] === 'critical_missing') {
                    $this->errors[] = "✗ CRÍTICO: Controller '{$controller}' NÃO ENCONTRADO";
                } elseif ($config['status'] === 'pending') {
                    $this->warnings[] = "⚠ Controller '{$controller}' pendente de implementação";
                }
            }
        }
    }

    protected function checkControllerMethods($controller, $controllerFile)
    {
        $content = File::get($controllerFile);

        // Controllers resource devem ter métodos CRUD
        if (
            str_contains($controller, 'ProductController') ||
            str_contains($controller, 'OrderController')
        ) {

            $resourceMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

            foreach ($resourceMethods as $method) {
                if (!str_contains($content, "public function {$method}(")) {
                    $this->info[] = "→ Controller '{$controller}' sem método '{$method}'";
                }
            }
        }

        // Verificar injeção de dependências
        if (str_contains($controller, 'Admin')) {
            if (!str_contains($content, 'middleware') && !str_contains($content, '__construct')) {
                $this->warnings[] = "⚠ Controller '{$controller}' pode precisar de middleware admin";
            }
        }

        // Verificar Form Requests
        if (str_contains($content, 'Request $request')) {
            if (!str_contains($content, 'FormRequest') && !str_contains($content, 'StoreRequest')) {
                $this->info[] = "→ Controller '{$controller}' usando Request genérico (considere Form Request)";
            }
        }
    }

    protected function checkRoutes()
    {
        $this->info('🛣️ VERIFICANDO ROTAS...');
        $this->newLine();

        $routes = Route::getRoutes();
        $routesByPrefix = [];
        $routesByMethod = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $methods = $route->methods();
            $action = $route->getActionName();
            $middlewares = $route->middleware();

            // Ignorar rotas do Laravel
            if (
                str_starts_with($uri, '_') ||
                str_starts_with($uri, 'sanctum') ||
                str_starts_with($uri, 'livewire')
            ) {
                continue;
            }

            // Agrupar por prefixo
            $prefix = explode('/', $uri)[0] ?? 'root';
            if (!isset($routesByPrefix[$prefix])) {
                $routesByPrefix[$prefix] = [];
            }
            $routesByPrefix[$prefix][] = [
                'uri' => $uri,
                'methods' => $methods,
                'action' => $action,
                'middlewares' => $middlewares
            ];

            // Agrupar por método HTTP
            foreach ($methods as $method) {
                if (!isset($routesByMethod[$method])) {
                    $routesByMethod[$method] = 0;
                }
                $routesByMethod[$method]++;
            }
        }

        // Verificar rotas esperadas
        $expectedPrefixes = ['admin', 'seller', 'cart', 'checkout', 'products', 'profile'];

        foreach ($expectedPrefixes as $prefix) {
            if (isset($routesByPrefix[$prefix])) {
                $count = count($routesByPrefix[$prefix]);
                $this->success[] = "✓ Prefixo '{$prefix}' configurado ({$count} rotas)";
            } else {
                if (in_array($prefix, ['cart', 'checkout', 'products'])) {
                    $this->errors[] = "✗ Prefixo crítico '{$prefix}' NÃO configurado";
                } else {
                    $this->warnings[] = "⚠ Prefixo '{$prefix}' não encontrado";
                }
            }
        }

        // Estatísticas de métodos HTTP
        $this->info[] = "→ Rotas por método: GET=" . ($routesByMethod['GET'] ?? 0) .
            ", POST=" . ($routesByMethod['POST'] ?? 0) .
            ", PUT=" . ($routesByMethod['PUT'] ?? 0) .
            ", DELETE=" . ($routesByMethod['DELETE'] ?? 0);

        // Verificar middlewares
        $this->checkRouteMiddlewares($routesByPrefix);
    }

    protected function checkRouteMiddlewares($routesByPrefix)
    {
        $issues = [];

        // Verificar rotas admin
        if (isset($routesByPrefix['admin'])) {
            foreach ($routesByPrefix['admin'] as $route) {
                $hasAdminMiddleware = false;
                foreach ($route['middlewares'] as $middleware) {
                    if (str_contains($middleware, 'admin') || str_contains($middleware, 'Admin')) {
                        $hasAdminMiddleware = true;
                        break;
                    }
                }
                if (!$hasAdminMiddleware) {
                    $issues[] = $route['uri'];
                }
            }

            if (!empty($issues)) {
                $this->errors[] = "✗ Rotas admin SEM middleware: " . implode(', ', array_slice($issues, 0, 3));
            }
        }

        // Verificar rotas seller
        $issues = [];
        if (isset($routesByPrefix['seller'])) {
            foreach ($routesByPrefix['seller'] as $route) {
                $hasSellerMiddleware = false;
                foreach ($route['middlewares'] as $middleware) {
                    if (str_contains($middleware, 'seller') || str_contains($middleware, 'Seller')) {
                        $hasSellerMiddleware = true;
                        break;
                    }
                }
                if (!$hasSellerMiddleware && !str_contains($route['uri'], 'onboarding')) {
                    $issues[] = $route['uri'];
                }
            }

            if (!empty($issues)) {
                $this->warnings[] = "⚠ Rotas seller sem middleware: " . implode(', ', array_slice($issues, 0, 3));
            }
        }

        // Verificar auth geral
        $publicRoutes = ['/', 'login', 'register', 'products', 'search'];
        $authRequiredPrefixes = ['profile', 'dashboard', 'orders'];

        foreach ($authRequiredPrefixes as $prefix) {
            if (isset($routesByPrefix[$prefix])) {
                foreach ($routesByPrefix[$prefix] as $route) {
                    if (!in_array('auth', $route['middlewares'])) {
                        $this->warnings[] = "⚠ Rota '{$route['uri']}' pode precisar de autenticação";
                    }
                }
            }
        }
    }

    protected function checkMiddlewares()
    {
        $this->info('🔐 VERIFICANDO MIDDLEWARES (Laravel 12)...');
        $this->newLine();

        $middlewarePath = app_path('Http/Middleware');

        // Laravel 12: Middlewares esperados com aliases
        $expectedMiddlewares = [
            'AdminMiddleware' => 'admin',
            'SellerMiddleware' => 'seller', 
            'VerifiedSellerMiddleware' => 'verified.seller',
            'SecurityHeadersMiddleware' => 'security.headers',
            'RateLimitMiddleware' => 'rate.limit',
            'ValidateFileUploadMiddleware' => 'validate.upload'
        ];

        // Verificar existência dos middlewares
        foreach ($expectedMiddlewares as $className => $alias) {
            $middlewareFile = $middlewarePath . '/' . $className . '.php';

            if (File::exists($middlewareFile)) {
                $this->success[] = "✓ Middleware '{$className}' encontrado";
                $this->checkMiddlewareContent($className, $middlewareFile, $alias);
            } else {
                if (in_array($className, ['AdminMiddleware', 'SellerMiddleware', 'VerifiedSellerMiddleware'])) {
                    $this->errors[] = "✗ CRÍTICO: Middleware '{$className}' NÃO ENCONTRADO";
                } else {
                    $this->warnings[] = "⚠ Middleware de segurança '{$className}' não implementado";
                }
            }
        }

        // Verificar estrutura Laravel 12: bootstrap/app.php
        $this->checkLaravel12MiddlewareStructure($expectedMiddlewares);
    }
    
    protected function checkMiddlewareContent($className, $middlewareFile, $alias)
    {
        $content = File::get($middlewareFile);
        
        // Verificar método handle obrigatório
        if (!str_contains($content, 'public function handle')) {
            $this->errors[] = "✗ Middleware '{$className}' sem método handle()";
            return;
        }
        
        // Verificar assinatura correta do método handle (Laravel 12)
        if (!str_contains($content, 'Request $request, Closure $next')) {
            $this->warnings[] = "⚠ Middleware '{$className}' pode ter assinatura incorreta do handle()";
        }
        
        // Verificar return type hint (Laravel 12)
        if (!str_contains($content, '): Response')) {
            $this->info[] = "→ Middleware '{$className}' sem type hint Response (recomendado Laravel 12)";
        }
        
        // Verificações específicas por middleware
        switch ($className) {
            case 'AdminMiddleware':
                if (!str_contains($content, 'isAdmin()') && !str_contains($content, "role === 'admin'")) {
                    $this->warnings[] = "⚠ AdminMiddleware pode não estar verificando role corretamente";
                }
                if (!str_contains($content, 'redirect')) {
                    $this->warnings[] = "⚠ AdminMiddleware sem redirecionamento para não-admin";
                }
                break;
                
            case 'SellerMiddleware':
                if (!str_contains($content, 'isSeller()') && !str_contains($content, "role === 'seller'")) {
                    $this->warnings[] = "⚠ SellerMiddleware pode não estar verificando role corretamente";
                }
                break;
                
            case 'VerifiedSellerMiddleware':
                if (!str_contains($content, 'sellerProfile') || !str_contains($content, 'canSellProducts')) {
                    $this->warnings[] = "⚠ VerifiedSellerMiddleware pode não verificar status de aprovação";
                }
                break;
                
            case 'SecurityHeadersMiddleware':
                // CSP para marketplace - permitir assets locais, CDNs e Vite (Laragon)
                $csp = "default-src 'self'; " .
                       "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com unpkg.com https://marketplace-b2c.test:5173 https://marketplace-b2c.test:5174; " .
                       "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdn.tailwindcss.com https://marketplace-b2c.test:5173 https://marketplace-b2c.test:5174; " .
                       "font-src 'self' fonts.gstatic.com; " .
                       "img-src 'self' data: blob: *.mercadopago.com *.mlstatic.com; " .
                       "connect-src 'self' api.mercadopago.com *.mercadopago.com https://marketplace-b2c.test:5173 https://marketplace-b2c.test:5174 wss://marketplace-b2c.test:5173 wss://marketplace-b2c.test:5174; " .
                       "frame-src 'self' *.mercadopago.com";

                // CSP mais permissivo para desenvolvimento Laragon
                $csp = "default-src 'self'; " .
                       "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com unpkg.com https://*.test:*; " .
                       "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdn.tailwindcss.com https://*.test:*; " .
                       "font-src 'self' fonts.gstatic.com; " .
                       "img-src 'self' data: blob: *.mercadopago.com *.mlstatic.com; " .
                       "connect-src 'self' api.mercadopago.com *.mercadopago.com https://*.test:* wss://*.test:*; " .
                       "frame-src 'self' *.mercadopago.com";

                // Verificar se o header Content-Security-Policy está presente e correto
                if (!str_contains($content, "Content-Security-Policy: {$csp}")) {
                    $this->warnings[] = "⚠ SecurityHeadersMiddleware sem header CSP correto";
                }
                break;
                
            case 'RateLimitMiddleware':
                if (!str_contains($content, 'RateLimiter') || !str_contains($content, 'tooManyAttempts')) {
                    $this->warnings[] = "⚠ RateLimitMiddleware pode não usar RateLimiter corretamente";
                }
                break;
                
            case 'ValidateFileUploadMiddleware':
                if (!str_contains($content, 'hasFile') || !str_contains($content, 'getClientOriginalExtension')) {
                    $this->warnings[] = "⚠ ValidateFileUploadMiddleware pode não validar arquivos corretamente";
                }
                break;
        }
    }
    
    protected function checkLaravel12MiddlewareStructure($expectedMiddlewares)
    {
        // Laravel 12: Verificar bootstrap/app.php (NÃO usar app/Http/Kernel.php)
        $bootstrapFile = base_path('bootstrap/app.php');
        
        if (!File::exists($bootstrapFile)) {
            $this->errors[] = "✗ CRÍTICO: Arquivo bootstrap/app.php não encontrado (obrigatório Laravel 12)";
            return;
        }
        
        $bootstrapContent = File::get($bootstrapFile);
        
        // Verificar estrutura básica do Laravel 12
        if (!str_contains($bootstrapContent, '->withMiddleware(function (Middleware $middleware)')) {
            $this->errors[] = "✗ CRÍTICO: bootstrap/app.php sem estrutura withMiddleware() do Laravel 12";
            return;
        }
        
        $this->success[] = "✓ Estrutura withMiddleware() do Laravel 12 encontrada";
        
        // Verificar se aliases estão registrados
        foreach ($expectedMiddlewares as $className => $alias) {
            if (!str_contains($bootstrapContent, "'{$alias}'")) {
                if (in_array($className, ['AdminMiddleware', 'SellerMiddleware'])) {
                    $this->errors[] = "✗ Middleware crítico '{$alias}' NÃO registrado em bootstrap/app.php";
                } else {
                    $this->warnings[] = "⚠ Middleware '{$alias}' não registrado em bootstrap/app.php";
                }
            } else {
                $this->success[] = "✓ Middleware '{$alias}' registrado no Laravel 12";
            }
        }
        
        // Verificar se middleware global está sendo aplicado (Laravel 12)
        if (str_contains($bootstrapContent, '->append(')) {
            $this->success[] = "✓ Middleware global configurado (append)";
        }
        
        // Verificar se usa o import correto do Laravel 12
        if (!str_contains($bootstrapContent, 'use Illuminate\Foundation\Configuration\Middleware')) {
            $this->warnings[] = "⚠ Import Middleware do Laravel 12 pode estar incorreto";
        }
        
        // Verificar se NÃO está usando Kernel.php (obsoleto desde Laravel 11)
        $kernelFile = app_path('Http/Kernel.php');
        if (File::exists($kernelFile)) {
            $this->warnings[] = "⚠ Arquivo app/Http/Kernel.php encontrado (obsoleto no Laravel 12 - usar bootstrap/app.php)";
        } else {
            $this->success[] = "✓ Sem Kernel.php obsoleto (correto para Laravel 12)";
        }
        
        $this->info[] = "→ Estrutura de middlewares compatível com Laravel 12";
    }
    protected function checkViews()
    {
        $this->info('👁️ VERIFICANDO VIEWS...');
        $this->newLine();

        $viewPath = resource_path('views');
        $viewFiles = File::allFiles($viewPath);

        $this->info[] = "→ Total de views: " . count($viewFiles);

        $fieldReferences = [];
        $undefinedVariables = [];
        $missingViews = [];

        foreach ($viewFiles as $file) {
            if ($file->getExtension() !== 'php') continue;

            $content = $file->getContents();
            $relativePath = str_replace($viewPath . '/', '', $file->getPathname());

            $this->checkFieldReferences($content, $relativePath, $fieldReferences);
            $this->checkUndefinedVariables($content, $relativePath, $undefinedVariables);
            $this->checkViewIncludes($content, $relativePath, $missingViews);
        }

        if (!empty($fieldReferences)) {
            $uniqueFields = array_unique(array_column($fieldReferences, 'field'));
            $this->warnings[] = "⚠ Campos suspeitos em views: " . implode(', ', array_slice($uniqueFields, 0, 5));
        }

        if (!empty($missingViews)) {
            foreach (array_unique($missingViews) as $view) {
                $this->warnings[] = "⚠ View não encontrada: '{$view}'";
            }
        }
    }

    protected function checkFieldReferences($content, $viewPath, &$fieldReferences)
    {
        $patterns = [
            '/\$\w+->(\w+)/',                    // $user->name
            '/\{\{\s*\$\w+->(\w+)\s*\}\}/',     // {{ $user->name }}
            '/old\([\'"](\w+)[\'"]\)/',         // old('name')
            '/name=[\'"](\w+)[\'"]/',           // name="field"
        ];

        $knownFields = $this->getKnownFields();

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $field) {
                    $fieldExists = false;

                    foreach ($knownFields as $table => $fields) {
                        if (in_array($field, $fields)) {
                            $fieldExists = true;
                            break;
                        }
                    }

                    if (!$fieldExists && !$this->isCommonLaravelField($field)) {
                        $fieldReferences[] = [
                            'view' => $viewPath,
                            'field' => $field
                        ];
                    }
                }
            }
        }
    }

    protected function getKnownFields()
    {
        $knownFields = [];

        // Obter campos de todas as tabelas
        foreach (array_keys($this->expectedTables) as $table) {
            try {
                $columns = Schema::getColumnListing($table);
                $knownFields[$table] = $columns;
            } catch (\Exception $e) {
                // Tabela não existe
            }
        }

        return $knownFields;
    }

    protected function isCommonLaravelField($field)
    {
        $commonFields = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token',
            'email_verified_at',
            'password',
            'name',
            'email',
            'errors',
            'message',
            'status',
            'route',
            'url',
            'path',
            'method',
            'action',
            'user',
            'auth',
            'request',
            'session',
            'config',
            'app',
            'loop',
            'key',
            'value',
            'item'
        ];

        return in_array(strtolower($field), $commonFields);
    }

    protected function checkUndefinedVariables($content, $viewPath, &$undefinedVariables)
    {
        preg_match_all('/\$([a-zA-Z_]\w*)/', $content, $matches);

        $commonVariables = [
            'errors',
            'request',
            'auth',
            'app',
            'config',
            'session',
            'loop',
            'slot',
            'component',
            'attributes'
        ];

        foreach ($matches[1] as $variable) {
            if (!in_array($variable, $commonVariables)) {
                if (
                    !str_contains($content, "@php") ||
                    !str_contains($content, "\${$variable} =") ||
                    !str_contains($content, "@foreach")
                ) {
                    $undefinedVariables[] = [
                        'view' => $viewPath,
                        'variable' => $variable
                    ];
                }
            }
        }
    }

    protected function checkViewIncludes($content, $viewPath, &$missingViews)
    {
        $patterns = [
            '/@extends\([\'"]([^\'"]+)[\'"]\)/',
            '/@include\([\'"]([^\'"]+)[\'"]\)/',
            '/@includeIf\([\'"]([^\'"]+)[\'"]\)/',
            '/@component\([\'"]([^\'"]+)[\'"]\)/',
            '/x-([a-z\-\.]+)/',  // Componentes Blade
        ];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);

            if (!empty($matches[1])) {
                foreach ($matches[1] as $viewName) {
                    // Converter componente x- para caminho
                    if (str_contains($viewName, '-')) {
                        $viewName = 'components.' . str_replace('-', '.', $viewName);
                    }

                    $viewFile = resource_path('views/' . str_replace('.', '/', $viewName) . '.blade.php');

                    if (!File::exists($viewFile)) {
                        $missingViews[] = $viewName;
                    }
                }
            }
        }
    }

    protected function generateDataDictionary()
    {
        $this->info('📚 GERANDO DICIONÁRIO DE DADOS...');
        $this->newLine();

        $markdown = "# Dicionário de Dados - Marketplace B2C\n\n";
        $markdown .= "Gerado em: " . now()->format('d/m/Y H:i:s') . "\n";
        $markdown .= "Laravel Version: " . app()->version() . "\n";
        $markdown .= "PHP Version: " . PHP_VERSION . "\n\n";

        $markdown .= "## Sumário\n\n";
        $markdown .= "| Tabela | Registros | Descrição |\n";
        $markdown .= "|--------|-----------|-----------||\n";

        foreach ($this->dataDictionary as $table => $data) {
            try {
                $count = DB::table($table)->count();
            } catch (\Exception $e) {
                $count = 'N/A';
            }
            $markdown .= "| [{$table}](#{$table}) | {$count} | {$data['description']} |\n";
        }

        $markdown .= "\n---\n\n";

        foreach ($this->dataDictionary as $table => $data) {
            $markdown .= "## <a name=\"{$table}\"></a>{$table}\n\n";
            $markdown .= "**Descrição:** {$data['description']}\n\n";

            try {
                $count = DB::table($table)->count();
                $markdown .= "**Total de Registros:** {$count}\n\n";

                // Estatísticas adicionais
                if ($count > 0) {
                    $latest = DB::table($table)->latest('created_at')->first();
                    if ($latest && isset($latest->created_at)) {
                        $markdown .= "**Último registro:** {$latest->created_at}\n\n";
                    }
                }
            } catch (\Exception $e) {
                $markdown .= "**Total de Registros:** Tabela não existe\n\n";
            }

            $markdown .= "### Campos\n\n";
            $markdown .= "| Campo | Tipo | Nulo | Chave | Padrão | Extra | Comentário |\n";
            $markdown .= "|-------|------|------|-------|--------|-------|------------|\n";

            if (isset($data['columns'])) {
                foreach ($data['columns'] as $column => $details) {
                    $null = $details['null'] ? '✓' : '✗';
                    $key = $this->formatKey($details['key']);
                    $default = $details['default'] ?? 'NULL';
                    $extra = $details['extra'] ?: '-';
                    $comment = $details['comment'] ?: '-';

                    $markdown .= "| **{$column}** | `{$details['type']}` | {$null} | {$key} | {$default} | {$extra} | {$comment} |\n";
                }
            }

            $markdown .= "\n### Relacionamentos\n\n";
            $relationships = $this->getTableRelationships($table);

            if (!empty($relationships)) {
                foreach ($relationships as $rel) {
                    $markdown .= "- {$rel}\n";
                }
            } else {
                $markdown .= "*Nenhum relacionamento documentado*\n";
            }

            $markdown .= "\n### Índices\n\n";
            $indexes = $this->getTableIndexes($table);

            if (!empty($indexes)) {
                $markdown .= "| Nome | Colunas | Tipo |\n";
                $markdown .= "|------|---------|------|\n";
                foreach ($indexes as $index) {
                    $markdown .= "| {$index['name']} | {$index['columns']} | {$index['type']} |\n";
                }
            } else {
                $markdown .= "*Apenas chave primária*\n";
            }

            $markdown .= "\n---\n\n";
        }

        $filename = storage_path('app/data_dictionary_' . date('Y-m-d_His') . '.md');
        File::put($filename, $markdown);

        $this->success[] = "✓ Dicionário salvo: {$filename}";
    }

    protected function formatKey($key)
    {
        $keys = [
            'PRI' => '🔑 PK',
            'UNI' => '🔐 UNIQUE',
            'MUL' => '🔗 FK',
            '' => '-'
        ];

        return $keys[$key] ?? $key;
    }

    protected function getTableRelationships($table)
    {
        $relationships = [
            'users' => [
                '→ hasOne: seller_profiles (user_id)',
                '→ hasMany: orders (user_id)',
                '→ hasMany: carts (user_id)'
            ],
            'seller_profiles' => [
                '← belongsTo: users (user_id)',
                '← belongsTo: users (approved_by)',
                '← belongsTo: users (rejected_by)',
                '→ hasMany: products (seller_id)',
                '→ hasMany: sub_orders (seller_id)',
                '→ hasMany: transactions (seller_id)'
            ],
            'products' => [
                '← belongsTo: seller_profiles (seller_id)',
                '← belongsTo: categories (category_id)',
                '→ hasMany: product_images (product_id)',
                '→ hasMany: product_variations (product_id)',
                '→ hasMany: cart_items (product_id)',
                '→ hasMany: order_items (product_id)'
            ],
            'orders' => [
                '← belongsTo: users (user_id)',
                '→ hasMany: order_items (order_id)',
                '→ hasMany: sub_orders (order_id)',
                '→ hasMany: transactions (order_id)'
            ],
            'sub_orders' => [
                '← belongsTo: orders (order_id)',
                '← belongsTo: seller_profiles (seller_id)',
                '→ hasMany: order_items (sub_order_id)'
            ]
        ];

        return $relationships[$table] ?? [];
    }

    protected function getTableIndexes($table)
    {
        $indexes = [];

        try {
            $rawIndexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name != 'PRIMARY'");

            $groupedIndexes = [];
            foreach ($rawIndexes as $index) {
                if (!isset($groupedIndexes[$index->Key_name])) {
                    $groupedIndexes[$index->Key_name] = [
                        'name' => $index->Key_name,
                        'columns' => [],
                        'type' => $index->Non_unique ? 'INDEX' : 'UNIQUE'
                    ];
                }
                $groupedIndexes[$index->Key_name]['columns'][] = $index->Column_name;
            }

            foreach ($groupedIndexes as $index) {
                $index['columns'] = implode(', ', $index['columns']);
                $indexes[] = $index;
            }
        } catch (\Exception $e) {
            // Ignorar se tabela não existe
        }

        return $indexes;
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('                      📊 RESUMO FINAL');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        if (!empty($this->success)) {
            $this->info('✅ SUCESSOS (' . count($this->success) . ')');
            foreach (array_slice($this->success, 0, 5) as $msg) {
                $this->line('   ' . $msg);
            }
            if (count($this->success) > 5) {
                $this->line('   ... mais ' . (count($this->success) - 5) . ' sucessos');
            }
            $this->newLine();
        }

        if (!empty($this->warnings)) {
            $this->warn('⚠️  AVISOS (' . count($this->warnings) . ')');
            foreach (array_slice($this->warnings, 0, 10) as $msg) {
                $this->warn('   ' . $msg);
            }
            if (count($this->warnings) > 10) {
                $this->warn('   ... mais ' . (count($this->warnings) - 10) . ' avisos');
            }
            $this->newLine();
        }

        if (!empty($this->errors)) {
            $this->error('❌ ERROS CRÍTICOS (' . count($this->errors) . ')');
            foreach ($this->errors as $msg) {
                $this->error('   ' . $msg);
            }
            $this->newLine();
        }

        if (!empty($this->info)) {
            $this->info('ℹ️  INFORMAÇÕES (' . count($this->info) . ')');
            foreach (array_slice($this->info, 0, 5) as $msg) {
                $this->line('   ' . $msg);
            }
            $this->newLine();
        }

        $healthScore = $this->calculateHealthScore();
        $this->newLine();

        $scoreBar = $this->generateScoreBar($healthScore);
        $this->line($scoreBar);
        $this->info("🏆 SCORE DE SAÚDE: {$healthScore}%");

        if ($healthScore >= 90) {
            $this->info("   Estado: EXCELENTE ⭐⭐⭐⭐⭐");
        } elseif ($healthScore >= 70) {
            $this->warn("   Estado: BOM ⭐⭐⭐⭐");
        } elseif ($healthScore >= 50) {
            $this->warn("   Estado: REGULAR ⭐⭐⭐");
        } else {
            $this->error("   Estado: CRÍTICO ⭐⭐");
        }

        $this->newLine();
        $this->line("📅 Verificação concluída em: " . now()->format('d/m/Y H:i:s'));
    }

    protected function generateScoreBar($score)
    {
        $filled = floor($score / 5);
        $bar = '   [';

        for ($i = 0; $i < 20; $i++) {
            if ($i < $filled) {
                $bar .= '█';
            } else {
                $bar .= '░';
            }
        }

        $bar .= ']';
        return $bar;
    }

    protected function calculateHealthScore()
    {
        $totalChecks = count($this->success) + count($this->warnings) + count($this->errors);

        if ($totalChecks === 0) {
            return 0;
        }

        $score = 100;
        $score -= (count($this->errors) * 10);
        $score -= (count($this->warnings) * 2);

        return max(0, min(100, $score));
    }

    protected function exportReport()
    {
        $report = [
            'metadata' => [
                'generated_at' => now()->toIso8601String(),
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'database' => config('database.default'),
                'environment' => app()->environment()
            ],
            'health_score' => $this->calculateHealthScore(),
            'summary' => [
                'total_checks' => count($this->success) + count($this->warnings) + count($this->errors),
                'errors' => count($this->errors),
                'warnings' => count($this->warnings),
                'success' => count($this->success),
                'info' => count($this->info)
            ],
            'details' => [
                'errors' => $this->errors,
                'warnings' => $this->warnings,
                'success' => $this->success,
                'info' => $this->info
            ],
            'data_dictionary' => $this->dataDictionary,
            'recommendations' => $this->generateRecommendations()
        ];

        $filename = storage_path('app/consistency_report_' . date('Y-m-d_His') . '.json');
        File::put($filename, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("📄 Relatório exportado: {$filename}");
    }

    protected function generateRecommendations()
    {
        $recommendations = [];

        if (in_array("✗ CRÍTICO: Controller 'Seller/ProductController' NÃO ENCONTRADO", $this->errors)) {
            $recommendations[] = [
                'priority' => 'CRITICAL',
                'issue' => 'ProductController não implementado',
                'impact' => 'Vendedores não podem cadastrar produtos',
                'solution' => 'php artisan make:controller Seller/ProductController --resource'
            ];
        }

        if (count($this->warnings) > 10) {
            $recommendations[] = [
                'priority' => 'HIGH',
                'issue' => 'Muitos avisos detectados',
                'impact' => 'Possíveis problemas de manutenibilidade',
                'solution' => 'Revisar avisos e corrigir progressivamente'
            ];
        }

        foreach ($this->errors as $error) {
            if (str_contains($error, 'NÃO ENCONTRADO')) {
                $recommendations[] = [
                    'priority' => 'HIGH',
                    'issue' => $error,
                    'impact' => 'Funcionalidade incompleta',
                    'solution' => 'Implementar componente faltante'
                ];
            }
        }

        return $recommendations;
    }
}
