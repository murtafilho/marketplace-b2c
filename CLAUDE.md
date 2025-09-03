# Instruções para Claude Code

## Ambiente de Desenvolvimento
Este projeto usa **Laragon** como ambiente de desenvolvimento local no Windows.

## Terminal Padrão
**SEMPRE use o Cmder (terminal do Laragon)** para executar comandos. O Cmder já está configurado com todas as variáveis de ambiente necessárias:
- PHP 8.3.18
- MySQL 8.0.30
- Composer
- Node.js
- Git

## Comandos Importantes
- **Laravel Artisan**: Use `php artisan` diretamente (PHP já está no PATH)
- **Composer**: Use `composer` diretamente
- **NPM**: Use `npm` diretamente
- **MySQL**: Use `mysql -u root` (sem senha por padrão no Laragon)

## Caminhos Importantes
- **Laragon Root**: `C:\laragon`
- **Projeto**: `C:\laragon\www\marketplace-b2c`
- **PHP**: `C:\laragon\bin\php\php-8.3.18-nts-Win32-vs16-x64\php.exe`
- **Cmder**: `C:\laragon\bin\cmder\Cmder.exe`

## Notas Específicas do Projeto
- Este é um projeto Laravel com Vue.js
- Banco de dados: `marketplace_b2c`
- URL local: `http://marketplace-b2c.test`

## Comandos de Verificação
Antes de fazer alterações, sempre execute:
```bash
php artisan test
npm run lint
npm run build
```