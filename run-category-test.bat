@echo off
echo ========================================
echo   DIAGNÓSTICO COMPLETO DE CATEGORIAS
echo ========================================
echo.

echo 🔍 Executando testes de diagnóstico...
echo.

php artisan test tests/Feature/CategoryDisplayTest.php --verbose

echo.
echo ========================================
echo   DIAGNÓSTICO CONCLUÍDO
echo ========================================
echo.
echo 💡 Se encontrou problemas:
echo    1. Execute: php artisan db:seed
echo    2. Execute: php artisan categories:create-images
echo    3. Teste novamente a homepage
echo.
pause