@echo off
echo ==========================================
echo    SETUP: Sistema de Imagens - Categorias
echo ==========================================
echo.

echo 📁 Criando diretórios necessários...
mkdir "storage\app\public\categories" 2>nul
echo    ✅ storage/app/public/categories

echo.
echo 🔗 Criando link simbólico storage...
php artisan storage:link

echo.
echo 🎨 Gerando imagens padrão para categorias...
php artisan categories:create-images

echo.
echo 🗄️  Executando migration com imagens padrão...
php artisan migrate

echo.
echo ==========================================
echo ✅ SETUP CONCLUÍDO COM SUCESSO!
echo ==========================================
echo.
echo 📋 Sistema configurado:
echo    • Storage link: public/storage ➜ storage/app/public
echo    • Diretório: storage/app/public/categories
echo    • Comando: php artisan categories:create-images
echo    • Controller: App\Http\Controllers\Admin\CategoryController
echo    • Service: App\Services\CategoryImageService
echo.
echo 🌐 Acesso:
echo    • Admin: http://localhost/marketplace-b2c/public/admin/categories
echo    • Homepage: http://localhost/marketplace-b2c/public/
echo.
echo 💡 Para adicionar suas próprias imagens:
echo    1. Acesse o painel admin (/admin/categories)
echo    2. Edite a categoria desejada
echo    3. Faça upload da nova imagem (JPEG/PNG, máx 2MB)
echo    4. A imagem será automaticamente redimensionada
echo.
pause