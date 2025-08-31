#!/bin/bash

# Script de Deploy para Produção - Marketplace B2C
echo "🚀 INICIANDO DEPLOY PARA PRODUÇÃO"
echo "=================================="

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script na raiz do projeto Laravel"
    exit 1
fi

echo "📋 Verificando ambiente..."

# 1. Verificar dependências de produção
echo "1. 📦 Instalando dependências de produção..."
composer install --no-dev --optimize-autoloader

# 2. Instalar dependências NPM e build assets
echo "2. 🎨 Compilando assets para produção..."
npm ci
npm run build

# 3. Configurar arquivos de ambiente
echo "3. 🔧 Configurando ambiente de produção..."
if [ ! -f ".env" ]; then
    echo "   📝 Copiando .env.production para .env..."
    cp .env.production .env
else
    echo "   ⚠️  Arquivo .env já existe. Verificque se está configurado para produção."
fi

# 4. Gerar chave da aplicação se necessário
if grep -q "APP_KEY=base64:GENERATE_NEW_KEY_FOR_PRODUCTION" .env; then
    echo "   🔑 Gerando nova chave da aplicação..."
    php artisan key:generate
fi

# 5. Otimizações de cache
echo "4. ⚡ Aplicando otimizações de performance..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Executar migrações
echo "5. 🗄️  Executando migrações..."
php artisan migrate --force

# 7. Executar seeders críticos (se necessário)
echo "6. 🌱 Executando seeders de produção..."
php artisan db:seed --class=CategorySeeder --force
php artisan app:ensure-protected-users

# 8. Link do storage
echo "7. 🔗 Configurando link do storage..."
php artisan storage:link

# 9. Otimizar permissões
echo "8. 🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || echo "   ⚠️  Não foi possível alterar proprietário (execute como root se necessário)"

# 10. Verificar configuração
echo "9. 🔍 Verificando configuração final..."
php artisan app:check-consistency

echo ""
echo "✅ DEPLOY CONCLUÍDO COM SUCESSO!"
echo "================================"
echo ""
echo "🔧 PRÓXIMOS PASSOS MANUAIS:"
echo "1. Configure seu servidor web (Nginx/Apache)"
echo "2. Configure certificado SSL (Let's Encrypt)"
echo "3. Configure backups automáticos"
echo "4. Configure monitoramento"
echo "5. Teste todas as funcionalidades"
echo ""
echo "📋 VERIFICAÇÕES IMPORTANTES:"
echo "- APP_DEBUG=false no .env"
echo "- APP_ENV=production no .env"  
echo "- Configurar credenciais reais do Mercado Pago"
echo "- Configurar email SMTP"
echo "- Configurar AWS S3 se usado"
echo "- Configurar Redis se usado"
echo ""
echo "🚨 SEGURANÇA:"
echo "- Remova arquivos de debug/teste"
echo "- Configure headers de segurança"
echo "- Configure firewall"
echo "- Configure rate limiting"