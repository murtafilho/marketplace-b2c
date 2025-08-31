# Configurações do Ambiente - Claude AI

## Informações do Ambiente

- **Sistema Operacional**: Windows
- **Servidor Local**: Laragon
- **Shell Preferido**: Git Bash
- **Versão do PHP**: 8.3.18
- **Framework**: Laravel 12.26.3

## Caminhos Configurados

### PHP e Artisan
```bash
# Comando PHP
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe

# Comando Artisan
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan

# Composer
composer
```

### URLs do Projeto
- **URL Local**: http://marketplace-b2c.test
- **phpMyAdmin**: http://localhost/phpmyadmin

## Arquivos de Configuração

- `environment.json`: Configurações detalhadas do ambiente
- `settings.local.json`: Configurações específicas do Claude AI

## Notas Importantes

1. **Sempre usar Git Bash** para comandos de terminal
2. **Caminhos Unix**: No Git Bash, usar formato `/c/laragon/...` em vez de `C:\laragon\...`
3. **Laragon**: Gerencia hosts virtuais automaticamente
4. **Banco de Dados**: Acessível via phpMyAdmin sem senha

## Comandos Testados

✅ PHP Version: `PHP 8.3.18 (cli)`  
✅ Laravel Version: `Laravel Framework 12.26.3`  
✅ Artisan: Funcionando corretamente

---

*Configurações salvas automaticamente pelo Claude AI para otimizar futuras interações.*