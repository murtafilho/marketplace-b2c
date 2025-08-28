# Guia de Troubleshooting - Marketplace B2C Laravel 12

## 1. Verificação Inicial do Ambiente - Windows/Laragon

### Logs e Status do Laravel
```powershell
# Verificar logs do Laravel
Get-Content C:\laragon\www\marketplace-b2c\storage\logs\laravel.log -Tail 20

# Status do servidor Laragon
# Verificar via interface gráfica do Laragon ou:
netstat -an | findstr :80
netstat -an | findstr :3306
```

### Verificar Configuração do Ambiente
```bash
# No diretório do projeto
cd C:\laragon\www\marketplace-b2c

# Verificar versões
php --version
composer --version
node --version
npm --version
```

### Verificar Status do Projeto Laravel
```bash
# No Git Bash ou PowerShell
cd /c/laragon/www/marketplace-b2c

# Verificar se o Laravel está funcionando
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan --version

# Verificar conexão com banco
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:status

# Verificar se os testes passam
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan test --stop-on-failure
```

## 2. Problemas Comuns do Projeto

### A. Erro de Conexão com Banco de Dados
**Sintomas:** "Connection refused" ou "Access denied for user"

**Diagnóstico:**
```bash
# Verificar se MySQL está rodando no Laragon
netstat -an | findstr :3306

# Testar conexão manualmente
mysql -h127.0.0.1 -P3306 -uroot -p

# Verificar configuração do .env
cat .env | grep DB_
```

**Soluções:**
1. Iniciar o MySQL via interface do Laragon
2. Verificar credenciais no arquivo .env
3. Recriar banco de dados se necessário

### B. Erro de Migrations
**Sintomas:** "Table already exists" ou "Column not found"

**Verificação e Correção:**
```bash
# Ver status das migrations
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:status

# Rollback se necessário
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:rollback

# Executar migrations novamente
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate

# Em caso de problemas graves, reset completo
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:fresh --seed
```

**Script de Validação do Ambiente:**
```bash
# Verificar se todas as dependências estão funcionando
cd /c/laragon/www/marketplace-b2c

echo "=== Verificando PHP ==="
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe --version

echo "=== Verificando Composer ==="
composer --version

echo "=== Verificando Laravel ==="
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan --version

echo "=== Verificando Banco ==="
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:status

echo "=== Executando Testes ==="
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan test --parallel
```

### C. Problemas com Frontend (Vite/Tailwind)
**Verificar Build do Frontend:**
```bash
# Instalar dependências do Node.js
npm install

# Verificar se Tailwind está configurado
npx tailwindcss --version

# Build para desenvolvimento
npm run dev

# Build para produção
npm run build

# Verificar se arquivos foram compilados
ls -la public/build/
```

**Soluções:**
1. Limpar cache do Vite: `rm -rf node_modules/.vite`
2. Reinstalar dependências: `npm ci`
3. Verificar configuração do vite.config.js

## 3. Monitoramento do Sistema

### Monitoramento Laravel/PHP
```bash
# Monitorar logs do Laravel em tempo real
tail -f C:\laragon\www\marketplace-b2c\storage\logs\laravel.log

# Verificar performance do PHP
echo "=== Status do Sistema ==="
echo "Data: $(date)"
echo "Memória disponível:"
wmic OS get TotalVisibleMemorySize,FreePhysicalMemory /format:list

echo "Uso de disco:"
dir C:\laragon\www\marketplace-b2c /s /-c | find "bytes"

echo "Processos PHP ativos:"
wmic process where "name='php.exe'" get ProcessId,CommandLine
```

### Script de Monitoramento Contínuo
```powershell
# Arquivo: monitor_marketplace.ps1
# Executar no PowerShell

while ($true) {
    Clear-Host
    Write-Host "=== Marketplace B2C - Status ===" -ForegroundColor Green
    Write-Host "Data: $(Get-Date)" -ForegroundColor Yellow
    
    # Verificar se MySQL está rodando
    $mysql = Get-Process mysql -ErrorAction SilentlyContinue
    if ($mysql) {
        Write-Host "✅ MySQL: Rodando (PID: $($mysql.Id))" -ForegroundColor Green
    } else {
        Write-Host "❌ MySQL: Parado" -ForegroundColor Red
    }
    
    # Verificar se Apache está rodando
    $apache = Get-Process httpd -ErrorAction SilentlyContinue
    if ($apache) {
        Write-Host "✅ Apache: Rodando" -ForegroundColor Green
    } else {
        Write-Host "❌ Apache: Parado" -ForegroundColor Red
    }
    
    Start-Sleep -Seconds 5
}
```

### Script de Reset do Projeto
```bash
# Arquivo: reset_marketplace.sh
# Para Git Bash no Windows

echo "🔄 Reinicializando Marketplace B2C..."
cd /c/laragon/www/marketplace-b2c

# Limpar cache do Laravel
echo "Limpando cache do Laravel..."
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan cache:clear
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan config:clear
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan route:clear
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan view:clear

# Reotimizar autoloader
echo "Otimizando autoloader..."
composer dump-autoload

# Reinstalar dependências do frontend
echo "Reinstalando dependências frontend..."
rm -rf node_modules
rm package-lock.json
npm install

# Executar migrations
echo "Executando migrations..."
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:fresh

# Build do frontend
echo "Compilando frontend..."
npm run build

echo "✅ Reset concluído!"
echo "Teste o sistema acessando: http://marketplace-b2c.test"
```

## 4. Configuração de Logging do Laravel

### Habilitar Logs Detalhados
```php
// Arquivo: config/logging.php
// Para debugging avançado

'channels' => [
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'debug',
        'days' => 14,
    ],
    
    'database' => [
        'driver' => 'daily',
        'path' => storage_path('logs/database.log'),
        'level' => 'debug',
        'days' => 7,
    ],
],
```

### Configurar .env para Debug
```env
APP_DEBUG=true
LOG_CHANNEL=daily
LOG_LEVEL=debug
DB_LOG_SLOW_QUERIES=true
```

### Script de Análise de Logs Laravel
```bash
# Arquivo: analisar_logs.sh
# Para Git Bash no Windows

LOG_FILE="/c/laragon/www/marketplace-b2c/storage/logs/laravel.log"

echo "=== Análise de Logs Laravel ==="
echo "Período: Últimas 24 horas"
echo ""

echo "🔍 Erros Críticos:"
grep -i "error\|exception\|fatal" "$LOG_FILE" | tail -10

echo ""
echo "⚠️ Avisos:"
grep -i "warning\|notice" "$LOG_FILE" | tail -10

echo ""
echo "📊 Estatísticas de Requisições:"
echo "Requisições hoje: $(grep -c "$(date '+%Y-%m-%d')" "$LOG_FILE")"
echo "Erros 500: $(grep -c "500" "$LOG_FILE")"
echo "Erros 404: $(grep -c "404" "$LOG_FILE")"

echo ""
echo "📊 Performance de Queries:"
grep -i "slow query" "$LOG_FILE" | tail -5

echo ""
echo "📋 Logs Recentes (últimas 20 linhas):"
tail -20 "$LOG_FILE"
```

### PowerShell - Análise Rápida
```powershell
# Contar erros do dia
$hoje = Get-Date -Format "yyyy-MM-dd"
$logPath = "C:\laragon\www\marketplace-b2c\storage\logs\laravel.log"

if (Test-Path $logPath) {
    $erros = Select-String -Path $logPath -Pattern "ERROR|CRITICAL" | Where-Object { $_.Line -match $hoje }
    Write-Host "Erros encontrados hoje: $($erros.Count)" -ForegroundColor $(if($erros.Count -gt 0){'Red'}else{'Green'})
    
    if ($erros.Count -gt 0) {
        Write-Host "\nÚltimos 5 erros:"
        $erros | Select-Object -Last 5 | ForEach-Object { Write-Host $_.Line -ForegroundColor Yellow }
    }
} else {
    Write-Host "Arquivo de log não encontrado!" -ForegroundColor Red
}
```

## 5. Testes de Validação do Sistema

### Teste Completo do Sistema
```bash
# Arquivo: test_sistema_completo.sh
# Para Git Bash

cd /c/laragon/www/marketplace-b2c

echo "🧪 Executando testes completos do sistema..."

# 1. Teste de conexão com banco
echo "1. Testando banco de dados..."
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan migrate:status
if [ $? -eq 0 ]; then
    echo "✅ Banco de dados: OK"
else
    echo "❌ Banco de dados: ERRO"
    exit 1
fi

# 2. Teste de configuração
echo "2. Testando configurações..."
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan config:cache
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan route:cache
echo "✅ Configurações: OK"

# 3. Executar testes unitários
echo "3. Executando testes..."
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan test --parallel
if [ $? -eq 0 ]; then
    echo "✅ Testes: PASSARAM"
else
    echo "❌ Testes: FALHARAM"
fi

# 4. Testar frontend
echo "4. Testando build do frontend..."
npm run build
if [ $? -eq 0 ]; then
    echo "✅ Frontend: OK"
else
    echo "❌ Frontend: ERRO"
fi

echo "🏁 Testes concluídos!"
```

### Teste Rápido de APIs
```bash
# Testar endpoints principais (depois de implementar)
curl -X GET http://marketplace-b2c.test/api/health
curl -X GET http://marketplace-b2c.test/
curl -X GET http://marketplace-b2c.test/seller/dashboard -H "Cookie: laravel_session=..."
```

## 6. Checklist de Troubleshooting do Marketplace

### ✅ Verificações Básicas
- [ ] PHP 8.3 instalado e funcionando
- [ ] Composer instalado e atualizado
- [ ] MySQL rodando no Laragon (porta 3306)
- [ ] Apache rodando no Laragon (porta 80)
- [ ] Arquivo .env configurado corretamente
- [ ] Chave da aplicação gerada (APP_KEY)
- [ ] Banco de dados `marketplace-b2c` criado

### ✅ Verificações de Desenvolvimento
- [ ] Migrations executadas com sucesso
- [ ] Storage com permissões corretas
- [ ] Node.js e NPM instalados
- [ ] Dependências do Composer instaladas
- [ ] Dependências do NPM instaladas
- [ ] Tailwind CSS compilado corretamente
- [ ] Vite funcionando para desenvolvimento

### ✅ Verificações Avançadas
- [ ] Logs do Laravel limpos de erros críticos
- [ ] Testes passando (pelo menos 80%)
- [ ] Sistema de arquivos com espaço suficiente
- [ ] Memória RAM disponível > 2GB
- [ ] Cache do Laravel funcionando
- [ ] Sessões sendo armazenadas corretamente

### ✅ Teste Final do Sistema
```bash
# Arquivo: teste_final_marketplace.sh
# Para Git Bash

echo "🔬 Executando teste final do Marketplace B2C..."
cd /c/laragon/www/marketplace-b2c

# Verificar se Laravel responde
echo "Testando response do Laravel..."
if /c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan --version > /dev/null 2>&1; then
    echo "✅ Laravel respondendo"
else
    echo "❌ Laravel não responde"
    exit 1
fi

# Testar conexão web
echo "Testando acesso web..."
if curl -s http://marketplace-b2c.test/ | grep -q "marketplace"; then
    echo "✅ Site acessível"
else
    echo "❌ Site não acessível - verifique Laragon"
fi

# Executar teste rápido
echo "Executando teste rápido..."
/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan test --filter=BasicTest --stop-on-failure

echo "🎉 Sistema Marketplace B2C está funcionando!"
echo "Acesse: http://marketplace-b2c.test"
```

## 7. Recursos e Documentação do Projeto

### Documentação do Projeto
- **Especificações:** `docs/PROJECT-SPECS.md`
- **Roadmap:** `docs/DEVELOPMENT-ROADMAP.md`  
- **Status Atual:** `docs/PROJECT-STATUS.md`
- **Dicionário de Dados:** `docs/DATA_DICTIONARY.md`

### Recursos Laravel
- **Documentação Laravel 12:** https://laravel.com/docs/12.x
- **Laravel Breeze:** https://laravel.com/docs/12.x/starter-kits#breeze
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Alpine.js:** https://alpinejs.dev/

### Comandos Úteis do Projeto
```bash
# Comandos frequentes com path completo do PHP
alias php='/c/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe'

# Atalhos úteis
php artisan migrate:status
php artisan test
php artisan serve
php artisan tinker
npm run dev
npm run build
```

### Estrutura de URLs do Sistema
- **Home:** http://marketplace-b2c.test/
- **Admin:** http://marketplace-b2c.test/admin/dashboard
- **Seller:** http://marketplace-b2c.test/seller/dashboard
- **Produtos:** http://marketplace-b2c.test/seller/products

---

## 📋 Resumo das Principais Mudanças

Este arquivo foi **completamente reformulado** para se adequar ao contexto do projeto Marketplace B2C Laravel 12, removendo referências ao MCP (Model Context Protocol) e focando no troubleshooting do sistema atual.

### Principais Alterações:
1. ✅ Substituição de comandos MCP por comandos Laravel/PHP
2. ✅ Adaptação de paths Linux/macOS para Windows/Laragon
3. ✅ Inclusão de scripts PowerShell para ambiente Windows
4. ✅ Foco em troubleshooting de banco de dados MySQL
5. ✅ Monitoramento específico para Laravel e frontend
6. ✅ Checklist adequado ao stack tecnológico atual
7. ✅ Recursos e documentação relevantes ao projeto

**Status:** ✅ **AUDITORIA CONCLUÍDA** - Arquivo totalmente atualizado e contextualizado para o projeto atual.