<?php

require_once 'vendor/autoload.php';

// Carregar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\LayoutHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

echo "=== LIMPANDO CACHE E TESTANDO URLS ===\n";

// Limpar cache do layout
LayoutHelper::clearCache();
echo "✅ Cache do layout limpo!\n";

// Limpar cache geral
Cache::flush();
echo "✅ Cache geral limpo!\n";

// Testar URLs
echo "\n=== TESTANDO URLS ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Logo URL: " . (LayoutHelper::getLogo() ?? 'Nenhuma logo configurada') . "\n";
echo "Site Name: " . LayoutHelper::getSiteName() . "\n";

echo "\n=== CACHE LIMPO E URLS TESTADAS ===\n";