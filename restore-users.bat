@echo off
echo 🔄 Restaurando usuários protegidos...
"C:/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe" artisan marketplace:ensure-protected-users
echo.
echo ✅ Pronto! Usuários restaurados.
pause