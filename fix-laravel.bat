@echo off
echo ========================================
echo   CORRIGINDO INSTALAÇÃO DO LARAVEL
echo ========================================
echo.

echo 🔍 1. Verificando se composer existe...
where composer
if %errorlevel% neq 0 (
    echo ❌ Composer não encontrado!
    echo 💡 Use o Laragon Terminal ou instale o Composer
    pause
    exit /b 1
)

echo.
echo 🔍 2. Verificando diretório atual...
dir /b vendor >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Diretório vendor não existe!
)

echo.
echo 🔧 3. Reinstalando dependências...
composer install --no-cache

echo.
echo 🔧 4. Se falhar, tentando update...
composer update

echo.
echo 🔧 5. Gerando autoload otimizado...
composer dump-autoload -o

echo.
echo ✅ Correção concluída!
echo 🌐 Teste: http://localhost/marketplace-b2c/public/simple-check.php
echo.
pause