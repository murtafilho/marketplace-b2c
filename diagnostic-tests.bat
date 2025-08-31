@echo off
echo ========================================
echo   DIAGNÓSTICO URGENTE DE TESTS FALHANDO
echo ========================================
echo.

echo 🔍 1. Verificando se Laravel está funcionando...
php artisan --version
if %errorlevel% neq 0 (
    echo ❌ Laravel não está funcionando!
    pause
    exit /b 1
)

echo.
echo 🔍 2. Verificando conexão com banco de dados...
php artisan tinker --execute="echo 'DB: ' . DB::connection()->getPdo()->getAttribute(PDO::ATTR_CONNECTION_STATUS);"
if %errorlevel% neq 0 (
    echo ❌ Problema com banco de dados!
    pause
    exit /b 1
)

echo.
echo 🔍 3. Executando ALL TESTS para identificar falhas...
echo.
php artisan test --stop-on-failure
if %errorlevel% neq 0 (
    echo.
    echo ❌ TESTS FALHARAM! Detalhes acima.
    echo.
    echo 💡 Possíveis soluções:
    echo    1. php artisan migrate:fresh --seed
    echo    2. php artisan cache:clear
    echo    3. php artisan config:clear
    echo    4. Verificar .env DATABASE_URL
    echo.
) else (
    echo.
    echo ✅ TODOS OS TESTS PASSARAM!
)

echo.
echo 🔍 4. Verificando estrutura de categorias...
php -r "
require_once 'bootstrap/app.php';
\$app = \Illuminate\Foundation\Application::getInstance();
\$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo '📊 Total categorias: ' . App\Models\Category::count() . PHP_EOL;
echo '🏠 Principais: ' . App\Models\Category::whereNull('parent_id')->count() . PHP_EOL;
echo '🖼️  Com imagens: ' . App\Models\Category::whereNotNull('image_path')->count() . PHP_EOL;

if (App\Models\Category::count() === 0) {
    echo '❌ BANCO VAZIO! Execute: php artisan db:seed' . PHP_EOL;
}
"

echo.
echo ========================================
echo   DIAGNÓSTICO CONCLUÍDO
echo ========================================
pause