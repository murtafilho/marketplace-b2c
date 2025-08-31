@echo off
echo ========================================
echo   EXECUTANDO TESTS COM STOP-ON-FAILURE
echo ========================================
echo.

echo 🧪 Executando todos os tests...
echo ⚠️  Irá parar no primeiro erro encontrado
echo.

php artisan test --stop-on-failure

echo.
if %errorlevel% neq 0 (
    echo ❌ TEST FALHOU! 
    echo.
    echo 🔍 INFORMAÇÕES DO ERRO:
    echo    - Veja a saída acima para detalhes
    echo    - O erro está no test que falhou
    echo.
    echo 💡 PRÓXIMOS PASSOS:
    echo    1. Copie a saída completa do erro
    echo    2. Informe qual test falhou
    echo    3. Mostre a mensagem de erro completa
    echo.
) else (
    echo ✅ TODOS OS TESTS PASSARAM!
    echo 🎉 Sistema funcionando corretamente
)

echo.
echo ========================================
pause