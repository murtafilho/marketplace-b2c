# ⚙️ Configurações do Sistema - Vale do Sol Marketplace

> Documentação completa das configurações e variáveis de ambiente

---

## 📋 Configurações Principais (.env)

### 🏗️ Aplicação Base

#### Desenvolvimento Local
```env
# Identidade da aplicação
APP_NAME="Vale do Sol"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=UTC

# URLs e domínio  
APP_URL=http://localhost/marketplace-b2c/public/
APP_DOMAIN=valedosol.org

# Localização
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
```

#### Produção (Preparado)
```env
# Identidade da aplicação
APP_NAME="Vale do Sol"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo

# URLs e domínio
APP_URL=https://valedosol.org
APP_DOMAIN=valedosol.org

# Localização
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR
```

### 🔐 Segurança

```env
# Chave de aplicação (gerada automaticamente)
APP_KEY=base64:bOXISPbpe74W79LGQ/lsZhotFT2sV1U/b3WP/5YiWnk=

# Rounds de criptografia
BCRYPT_ROUNDS=12

# Configurações de sessão
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

### 🗄️ Banco de Dados

```env
# Configuração MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace-b2c
DB_USERNAME=root
DB_PASSWORD=

# Para produção usar variáveis seguras:
# DB_HOST=${DB_HOST}
# DB_USERNAME=${DB_USERNAME}
# DB_PASSWORD=${DB_PASSWORD}
```

### 📧 Configurações de Email

#### Desenvolvimento (Log)
```env
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@valedosol.org"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Produção (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=${MAILGUN_USERNAME}
MAIL_PASSWORD=${MAILGUN_PASSWORD}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@valedosol.org"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔧 Configurações Laravel

### 📝 config/app.php

#### Configurações Customizadas
```php
<?php
return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'domain' => env('APP_DOMAIN', 'valedosol.org'), // Customizado
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'pt_BR'),         // Customizado
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'pt_BR'), // Customizado
    'faker_locale' => env('APP_FAKER_LOCALE', 'pt_BR'),       // Customizado
];
```

### 📊 Cache e Performance

```env
# Cache
CACHE_STORE=database
CACHE_PREFIX=

# Queue (para processamento assíncrono)
QUEUE_CONNECTION=database

# Broadcast (para notificações em tempo real)
BROADCAST_CONNECTION=log

# Filesystem
FILESYSTEM_DISK=local
```

### 🔄 Redis (Preparado para produção)

```env
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Para produção:
# REDIS_HOST=${REDIS_HOST}
# REDIS_PASSWORD=${REDIS_PASSWORD}
```

---

## 🎨 Configurações Frontend

### 💨 Tailwind CSS

#### tailwind.config.js
```javascript
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                // Cores Vale do Sol
                'vale-verde': {
                    DEFAULT: '#2F5233',
                    'light': '#3E6B42', 
                    'dark': '#1F3521',
                },
                'sol-dourado': {
                    DEFAULT: '#F4A460',
                    'light': '#F5B97A',
                    'dark': '#E89441',
                },
                'comercio-azul': {
                    DEFAULT: '#4A90E2',
                    'dark': '#3A7BC8',
                },
                'comunidade-roxo': '#9B59B6',
                'bg-light': '#F8F9FA',
                'text-primary': '#2C3E50',
            },
            boxShadow: {
                'soft': '0 2px 4px rgba(0,0,0,0.1)',
                'elevated': '0 8px 32px rgba(0,0,0,0.12)',
            },
        },
    },
    plugins: [],
}
```

### ⚡ Vite Configuration

#### vite.config.js
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
```

#### package.json (Dependências)
```json
{
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.7",
        "autoprefixer": "^10.4.16",
        "laravel-vite-plugin": "^1.0.0",
        "postcss": "^8.4.32",
        "tailwindcss": "^3.3.6",
        "vite": "^5.0.0"
    },
    "dependencies": {
        "alpinejs": "^3.13.3"
    }
}
```

---

## 📱 Configurações Mobile

### 📐 Meta Tags (Layout)

```html
<!-- Viewport otimizado -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">

<!-- PWA preparado -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

<!-- Otimizações -->
<meta name="format-detection" content="telephone=no">
<meta name="theme-color" content="#2F5233">
```

### 🎯 SEO e Open Graph

```html
<!-- SEO dinâmico -->
<title>@yield('title', config('app.name') . ' - Marketplace Comunitário')</title>
<meta name="description" content="@yield('description', config('app.name') . ' - O marketplace que conecta a comunidade local')">
<meta name="keywords" content="@yield('keywords', 'marketplace, vale do sol, produtos locais, comunidade')">

<!-- Open Graph -->
<meta property="og:title" content="@yield('title', config('app.name') . ' - Marketplace Comunitário')">
<meta property="og:description" content="@yield('description', 'O marketplace que conecta a comunidade local')">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title', config('app.name'))">
<meta name="twitter:description" content="@yield('description')">
```

---

## 🔒 Configurações de Segurança

### 🛡️ Proteções Implementadas

#### CSRF Protection
```php
// Automático em todos os formulários
@csrf

// Headers de segurança (preparado)
'headers' => [
    'X-Frame-Options' => 'DENY',
    'X-Content-Type-Options' => 'nosniff',
    'X-XSS-Protection' => '1; mode=block',
],
```

#### Validação de Dados
```php
// Middleware de validação automática
'throttle:api' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

// Sanitização de inputs
$request->validate([
    'name' => 'required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/',
    'email' => 'required|email|unique:users',
    'phone' => 'required|string|regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/',
]);
```

### 🔐 Autenticação

```env
# Session configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

# Password reset
MAIL_FROM_ADDRESS="noreply@valedosol.org"
```

---

## 🚀 Configurações de Deploy

### 🌐 Produção

#### Servidor Web (Apache/Nginx)
```apache
# .htaccess (Apache)
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# Virtual Host
<VirtualHost *:80>
    ServerName valedosol.org
    ServerAlias www.valedosol.org
    DocumentRoot /var/www/marketplace/public
    
    # Redirect HTTP to HTTPS
    RewriteEngine On
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName valedosol.org
    ServerAlias www.valedosol.org
    DocumentRoot /var/www/marketplace/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
</VirtualHost>
```

#### Comandos de Deploy
```bash
# Preparação do ambiente
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Assets
npm run build
php artisan storage:link

# Migrações
php artisan migrate --force

# Permissões
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 📊 Monitoramento

#### Logs
```env
# Configuração de logs
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Para produção:
LOG_LEVEL=error
LOG_CHANNEL=daily
```

#### Performance Monitoring (Preparado)
```env
# APM (Application Performance Monitoring)
# NEW_RELIC_LICENSE_KEY=${NEW_RELIC_KEY}
# SENTRY_DSN=${SENTRY_DSN}

# Debug bar (apenas desenvolvimento)
APP_DEBUG_BAR=true  # local only
```

---

## 🧪 Configurações de Teste

### 🔧 PHPUnit
```xml
<!-- phpunit.xml -->
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
    </testsuites>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</phpunit>
```

### 🎯 Environment Testing
```env
# .env.testing
APP_NAME="Vale do Sol Testing"
APP_ENV=testing
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
```

---

## 📚 Comandos Artisan Customizados

### ⚡ Scripts Úteis

```bash
# Configuração rápida
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Limpeza completa
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear

# Reset completo (desenvolvimento)
php artisan migrate:fresh --seed

# Verificação do sistema
php artisan about
php artisan env
```

### 🔧 Comandos Customizados (Preparado)
```bash
# Verificar integridade do banco
php artisan marketplace:check-database

# Limpar dados antigos
php artisan marketplace:cleanup

# Gerar relatórios
php artisan marketplace:reports
```

---

## 📋 Checklist de Configuração

### ✅ Desenvolvimento
- [x] APP_NAME definido
- [x] APP_URL configurado para localhost
- [x] Banco de dados configurado
- [x] Cache configurado (database)
- [x] Email configurado (log)
- [x] Debug ativo

### 🚀 Produção (Preparado)
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL=https://valedosol.org
- [ ] SSL certificado configurado
- [ ] Banco de dados produção
- [ ] Email SMTP configurado
- [ ] Cache Redis configurado
- [ ] Logs configurados
- [ ] Backup automatizado

---

*Configurações atualizadas em: Janeiro 2025*
*Projeto: Vale do Sol Marketplace*
*Status: Desenvolvimento local preparado para produção*