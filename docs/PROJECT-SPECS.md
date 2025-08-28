# MVP Marketplace B2C - Especificações Completas

## 📁 Estrutura de Arquivos de Documentação

```
projeto-marketplace/
├── docs/
│   ├── PROJECT-SPECS.md (este arquivo)
│   ├── TECHNICAL-STACK.md
│   ├── DATABASE-SCHEMA.md
│   ├── PAYMENT-FLOW.md
│   └── API-ENDPOINTS.md
├── .cursorrules
└── .env.example
```

## 🎯 Visão Geral do Projeto

**Objetivo**: MVP de marketplace B2C conectando vendedores e compradores com pagamento integrado via Mercado Pago e split automático.

## 🛠️ Stack Tecnológica

### Ambiente Base
- **OS**: Windows 11
- **Servidor**: Laragon
- **PHP**: 8.3
- **MySQL**: 8.0+
- **Laravel**: 12.x (Versão mais recente - Lançada em 2025)

### Frameworks e Bibliotecas
- **Backend**: Laravel 12.x
- **Frontend**: Alpine.js + Tailwind CSS
- **Build**: Vite
- **Pagamento**: Mercado Pago SDK
- **Pacotes Laravel**:
  - spatie/laravel-permission (quando necessário)
  - spatie/laravel-medialibrary (upload de imagens)

## 📋 Funcionalidades do MVP

### 1. Gestão de Usuários
- ✅ **IMPLEMENTADO** - Cadastro diferenciado (comprador/vendedor/admin)
- ✅ **IMPLEMENTADO** - Autenticação via Laravel Breeze
- ✅ **IMPLEMENTADO** - Verificação de email
- ✅ **IMPLEMENTADO** - Recuperação de senha
- ✅ **IMPLEMENTADO** - Roles: admin, seller, customer

### 2. Onboarding de Vendedores
- ✅ **IMPLEMENTADO** - Cadastro com CPF/CNPJ
- ✅ **IMPLEMENTADO** - Upload de comprovante de endereço
- ✅ **IMPLEMENTADO** - Aprovação manual pelo admin
- ❌ **NÃO IMPLEMENTADO** - Conexão obrigatória com Mercado Pago (OAuth)
- ✅ **IMPLEMENTADO** - Limite inicial: 100 produtos
- ✅ **IMPLEMENTADO** - Plano free inicialmente

### 3. Catálogo e Produtos
- ❌ **NÃO IMPLEMENTADO** - CRUD completo de produtos
- ❌ **NÃO IMPLEMENTADO** - Múltiplas imagens (até 5)
- ✅ **SCHEMA PRONTO** - Categorias e subcategorias
- ❌ **NÃO IMPLEMENTADO** - Variações (tamanho, cor)
- ❌ **NÃO IMPLEMENTADO** - Controle de estoque
- ❌ **NÃO IMPLEMENTADO** - Busca e filtros

### 4. Carrinho e Checkout
- ❌ **NÃO IMPLEMENTADO** - Carrinho unificado (múltiplos vendedores)
- ❌ **NÃO IMPLEMENTADO** - Pagamento único com split automático
- ❌ **NÃO IMPLEMENTADO** - Métodos: PIX (prioritário), Cartão, Boleto
- ❌ **NÃO IMPLEMENTADO** - Cálculo de frete por vendedor
- ❌ **NÃO IMPLEMENTADO** - Opções de entrega configuráveis

### 5. Sistema de Pagamento
- ❌ **NÃO IMPLEMENTADO** - Integração Mercado Pago
- ❌ **NÃO IMPLEMENTADO** - Split automático na aprovação
- ✅ **SCHEMA PRONTO** - Comissão configurável (padrão 10%)
- ✅ **SCHEMA PRONTO** - Override de comissão por vendedor
- ❌ **NÃO IMPLEMENTADO** - Webhook para confirmação instantânea

### 6. Painel Administrativo
- ✅ **IMPLEMENTADO** - Dashboard com métricas
- ✅ **IMPLEMENTADO** - Aprovação de vendedores
- ✅ **IMPLEMENTADO** - Gestão de comissões
- ✅ **IMPLEMENTADO** - Interface de gestão de vendedores
- ❌ **NÃO IMPLEMENTADO** - Moderação de produtos
- ❌ **NÃO IMPLEMENTADO** - Relatórios financeiros
- ❌ **NÃO IMPLEMENTADO** - Configurações do marketplace

## 🏗️ Arquitetura

### Estrutura de Controllers
```
app/Http/Controllers/
├── Admin/
│   ✅ DashboardController.php (IMPLEMENTADO)
│   ✅ SellerController.php (IMPLEMENTADO)
│   └── CommissionController.php
├── Seller/
│   ❌ ProductController.php (CRÍTICO - NÃO IMPLEMENTADO)
│   ✅ DashboardController.php (IMPLEMENTADO)
│   ✅ OnboardingController.php (IMPLEMENTADO)
│   └── OrderController.php
└── Shop/
    ✅ HomeController.php (IMPLEMENTADO)
    ├── ProductController.php
    ├── CartController.php
    └── CheckoutController.php
```

### Middlewares (Laravel 12)
```php
// bootstrap/app.php (NÃO USAR app/Http/Kernel.php - removido desde Laravel 11)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => AdminMiddleware::class,
        'seller' => SellerMiddleware::class,
        'verified.seller' => VerifiedSellerMiddleware::class,
    ]);
})
```

## 💾 Estrutura do Banco de Dados

### Tabelas Principais - ✅ TODAS IMPLEMENTADAS
- ✅ `users` (com role: admin/seller/customer)
- ✅ `seller_profiles` (dados adicionais do vendedor)
- ✅ `products`
- ✅ `product_images`
- ✅ `product_variations`
- ✅ `categories`
- ✅ `carts` / `cart_items`
- ✅ `orders` / `order_items`
- ✅ `sub_orders` (pedidos por vendedor)
- ✅ `transactions` (registro de pagamentos e splits)
- ✅ `seller_shipping_options`

### Convenções
- ✅ Snake_case (padrão Laravel)
- ✅ Tabelas no plural
- ✅ Soft deletes em: users, products, orders
- ✅ Timestamps em todas as tabelas
- ✅ IDs como BigInteger auto-increment

## 💳 Fluxo de Pagamento

### Checkout com PIX (Prioritário)
1. Cliente adiciona produtos de múltiplos vendedores
2. Checkout único → escolhe PIX
3. Mercado Pago gera QR Code
4. Cliente paga via app bancário
5. Webhook confirma pagamento
6. Split automático:
   - Marketplace recebe comissão (10% padrão)
   - Vendedor recebe valor - comissão
7. Vendedor pode sacar do MP quando quiser

### Configuração Mercado Pago
```env
# .env
MP_PUBLIC_KEY=TEST-xxxxx
MP_ACCESS_TOKEN=TEST-xxxxx
MP_APP_ID=xxxxx
MP_APP_FEE=10.0
MP_WEBHOOK_SECRET=xxxxx
```

## 🚢 Opções de Entrega

### Configuradas pelo Vendedor
- Frete Fixo
- Frete Grátis
- Retirar na Loja
- Combinar Entrega (WhatsApp)
- Tabela por Região

### Fluxo
1. Vendedor cadastra opções de entrega
2. Cliente escolhe uma opção por vendedor
3. Frete somado ao total
4. Vendedor recebe valor do frete junto

## 📝 Padrões de Código

### Cabeçalho Obrigatório
```php
<?php
/**
 * Arquivo: caminho/do/arquivo.php
 * Descrição: Breve descrição da funcionalidade
 * Laravel Version: 11.x
 * Criado em: DD/MM/YYYY
 */
```

### Validação com Form Requests
```php
// app/Http/Requests/Seller/StoreProductRequest.php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0.01'],
        // ...
    ];
}
```

## 🔐 Segurança

- CSRF Protection (padrão Laravel)
- XSS Prevention (escape output)
- SQL Injection (Eloquent ORM)
- Rate Limiting
- Validação de uploads
- Sanitização de inputs

## 📊 Métricas do Dashboard Admin

### Vendas
- Total (dia/semana/mês/ano)
- Ticket médio
- Produtos mais vendidos
- Gráfico de evolução

### Usuários
- Novos cadastros
- Vendedores ativos
- Taxa de conversão

### Operacional
- Pedidos pendentes
- Produtos em moderação
- Comissões a pagar

## 🎨 Frontend

### Tailwind CSS Classes
- Usar apenas classes utilitárias do Tailwind
- Não criar CSS customizado (exceto se necessário)
- Componentes Alpine.js para interatividade

### Estrutura de Views
```
resources/views/
├── layouts/
│   ├── app.blade.php
│   ├── admin.blade.php
│   └── seller.blade.php
├── admin/
├── seller/
└── shop/
```

## 🧪 Testes

### Estratégia MVP
- Apenas testes de features críticas
- Focar em: pagamento, pedidos, autenticação
- PHPUnit (padrão Laravel)

```bash
php artisan test --filter=PaymentTest
```

## 🚀 Comandos Iniciais

```bash
# Criar novo projeto Laravel 12
composer create-project laravel/laravel marketplace "12.*"

# Instalar dependências
composer require mercadopago/sdk
composer require laravel/breeze --dev

# Configurar Breeze
php artisan breeze:install blade
npm install
npm run dev

# Criar migrations
php artisan make:migration create_seller_profiles_table
php artisan make:migration create_products_table
# ... etc

# Criar models
php artisan make:model Product -mfsc
php artisan make:model Order -mfsc

# Criar controllers
php artisan make:controller Admin/DashboardController
php artisan make:controller Seller/ProductController --resource
```

## ⚠️ Pontos Críticos

1. **Laravel 12**: Usar nova sintaxe de middlewares (bootstrap/app.php)
2. **PHP 8.3**: Aproveitar typed properties e features novas
3. **Mercado Pago**: Vendedor DEVE ter conta conectada
3. **Split Payment**: Acontece automaticamente na aprovação
4. **Soft Deletes**: Implementar em users, products, orders
5. **Comissões**: Configurável global e por vendedor

## 📱 Responsividade

- Mobile-first approach
- Tailwind breakpoints: sm, md, lg, xl, 2xl
- Menu hamburguer no mobile
- Touch-friendly buttons

## 🔄 Fluxos Principais

### Fluxo do Vendedor
1. Cadastro → Envio docs → Aguarda aprovação
2. Aprovado → Conecta Mercado Pago
3. Conectado → Cadastra produtos
4. Produto aprovado → Disponível na loja
5. Venda → Recebe notificação
6. Envia produto → Atualiza status
7. Pagamento confirmado → Valor disponível no MP

### Fluxo do Comprador
1. Navega produtos → Adiciona ao carrinho
2. Checkout → Escolhe entrega por vendedor
3. Pagamento único → PIX/Cartão/Boleto
4. Confirmação → Acompanha pedidos
5. Recebe produtos → Avalia

## 🔧 Configurações Ambiente Dev

### .env.example
```env
APP_NAME="Marketplace B2C"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://marketplace-b2c.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace-b2c
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025

# Mercado Pago (Sandbox)
MP_PUBLIC_KEY=TEST-xxxxx
MP_ACCESS_TOKEN=TEST-xxxxx
MP_APP_ID=xxxxx
MP_REDIRECT_URI=${APP_URL}/seller/mercadopago/callback
MP_WEBHOOK_SECRET=xxxxx
MP_APP_FEE=10.0

# Marketplace Config
MARKETPLACE_COMMISSION=10.0
SELLER_AUTO_APPROVE=false
PRODUCT_AUTO_APPROVE=false
```

## 📚 Próximos Passos (Pós-MVP)

1. Sistema de avaliações
2. Chat vendedor-comprador
3. Cupons de desconto
4. Programa de fidelidade
5. App mobile
6. Multi-idioma
7. Dashboard analytics avançado
8. IA para recomendações

---

**IMPORTANTE**: Este documento deve ser mantido atualizado durante o desenvolvimento. Sempre consultar antes de implementar novas features.