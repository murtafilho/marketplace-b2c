@echo off
echo === Configurando Storage para Laravel no Windows/Laragon ===
echo.

cd /d C:\laragon\www\marketplace-b2c

echo 1. Verificando diretorio storage/app/public...
if not exist "storage\app\public" (
    echo    Criando diretorio...
    mkdir "storage\app\public"
    echo    OK - Diretorio criado
) else (
    echo    OK - Diretorio ja existe
)

echo.
echo 2. Removendo link/diretorio antigo se existir...
if exist "public\storage" (
    echo    Removendo public\storage...
    rmdir "public\storage" /s /q 2>nul
    del "public\storage" 2>nul
    echo    OK - Removido
) else (
    echo    OK - Nao existe
)

echo.
echo 3. Criando junction point (link simbolico para Windows)...
mklink /J "public\storage" "storage\app\public"
if %errorlevel% equ 0 (
    echo    OK - Junction point criado com sucesso
) else (
    echo    ERRO - Falha ao criar junction point
    echo    Tente executar como Administrador
)

echo.
echo 4. Limpando cache do Laravel...
C:\laragon\bin\php\php-8.2.19-Win32-vs16-x64\php.exe artisan config:clear
C:\laragon\bin\php\php-8.2.19-Win32-vs16-x64\php.exe artisan cache:clear
C:\laragon\bin\php\php-8.2.19-Win32-vs16-x64\php.exe artisan view:clear

echo.
echo 5. Verificando APP_URL no .env...
findstr /C:"APP_URL=http://localhost/marketplace-b2c/public" .env >nul
if %errorlevel% equ 0 (
    echo    OK - APP_URL configurada corretamente
) else (
    echo    ATENCAO - Verifique se APP_URL esta configurada como:
    echo    APP_URL=http://localhost/marketplace-b2c/public
)

echo.
echo === Configuracao concluida ===
echo.
echo Teste o upload de imagem em:
echo http://localhost/marketplace-b2c/public/admin/layout
echo.
pause