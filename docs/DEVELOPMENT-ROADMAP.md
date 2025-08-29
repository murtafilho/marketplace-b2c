# 🚀 ROTEIRO DE DESENVOLVIMENTO - MARKETPLACE B2C

## 📁 FASE 0: PREPARAÇÃO DO AMBIENTE

### 0.1 - Criar Estrutura Inicial
```bash
# Terminal do Windows (PowerShell/CMD)
mkdir marketplace-b2c
cd marketplace-b2c
```

### 0.2 - Copiar Documentação
1. Criar pasta `docs/`
2. Salvar `PROJECT-SPECS.md` em `docs/`
3. Salvar `.cursorrules` na raiz
4. Salvar este arquivo como `docs/DEVELOPMENT-ROADMAP.md`

### 0.3 - Abrir no Cursor
```bash
# Abrir Cursor na pasta do projeto
cursor .
```

---

## 🏗️ FASE 1: ESTRUTURA BASE (Dia 1)

### COMANDO 1.1 - Criar Projeto Laravel
```
@Claude: "Create a new Laravel 12 project with all initial configurations. Set up the project structure following the PROJECT-SPECS.md, including .env.example with all necessary variables for Mercado Pago and marketplace settings"
```

**✅ TESTE 1.1:**
```bash
# Terminal
composer install
cp .env.example .env
php artisan key:generate
php artisan --version  # Deve mostrar Laravel 12.x
```

### COMANDO 1.2 - Instalar Dependências
```
@Claude: "Install and configure Laravel Breeze with Blade, add Mercado Pago SDK, and set up Tailwind CSS with Vite. Configure everything for a marketplace following our specifications"
```

**✅ TESTE 1.2:**
```bash
npm install
npm run build
php artisan breeze:install blade --pest
# Verificar se Tailwind está funcionando
npm run dev
```

### COMANDO 1.3 - Configurar Bootstrap/App
```
@Claude: "Configure bootstrap/app.php for Laravel 12 with our marketplace middleware structure (admin, seller, verified.seller). Add rate limiting for checkout and API endpoints"
```

**✅ TESTE 1.3:**
```bash
php artisan route:list  # Ver middlewares registrados
```

---

## 💾 FASE 2: BANCO DE DADOS (Dia 1-2)

### COMANDO 2.1 - Criar Todas as Migrations
```
@Claude: "Create all database migrations for the marketplace following the database structure in PROJECT-SPECS.md. Include: users with roles, seller_profiles, products, product_images, categories, carts, cart_items, orders, sub_orders, transactions, seller_shipping_options. Use Laravel conventions with soft deletes where specified"
```

**✅ TESTE 2.1:**
```bash
# Verificar migrations criadas
ls database/migrations/
php artisan migrate:status
```

### COMANDO 2.2 - Executar Migrations
```
@Claude: "Review all migrations for errors and create a DatabaseSeeder with initial data: create admin user, 3 test sellers, 10 categories, and 20 sample products"
```

**✅ TESTE 2.2:**
```bash
# Criar banco no MySQL primeiro (via Laragon)
php artisan migrate:fresh
php artisan db:seed
# Verificar no phpMyAdmin se tabelas foram criadas
```

### COMANDO 2.3 - Criar Models com Relationships
```
@Claude: "Create all Eloquent models with proper relationships, fillables, casts, and scopes. Models needed: User, SellerProfile, Product, ProductImage, Category, Cart, CartItem, Order, SubOrder, OrderItem, Transaction, SellerShippingOption. Follow Laravel 12 conventions"
```

**✅ TESTE 2.3:**
```bash
php artisan tinker
>>> User::count()  # Deve retornar número de usuários
>>> Product::with('seller', 'category')->first()  # Testar relationships
```

---

## 🔐 FASE 3: AUTENTICAÇÃO E AUTORIZAÇÃO (Dia 2)

### COMANDO 3.1 - Configurar Multi-Auth
```
@Claude: "Modify Breeze authentication to support our three user roles (admin, seller, customer). Create middleware for each role and registration flows for sellers and customers with different forms"
```

**✅ TESTE 3.1:**
```bash
# Testar registro
php artisan serve
# Acessar http://localhost:8000/register
# Criar um customer e um seller
```

### COMANDO 3.2 - Criar Middlewares
```
@Claude: "Create AdminMiddleware, SellerMiddleware, and VerifiedSellerMiddleware. Sellers can access dashboard only after admin approval and Mercado Pago connection"
```

**✅ TESTE 3.2:**
```bash
# Testar redirecionamentos
# Login como customer -> não acessa /admin
# Login como seller não aprovado -> não acessa /seller/products
```

### COMANDO 3.3 - Criar Páginas de Onboarding
```
@Claude: "Create seller onboarding flow: registration form with CPF/CNPJ, file upload for address proof, pending approval page, and admin approval interface"
```

**✅ TESTE 3.3:**
- Registrar novo seller
- Upload de documento
- Ver status "pendente"
- Admin aprovar
- Seller pode acessar dashboard

---

## 🔧 FASE 3.5: SISTEMA ADMINISTRATIVO COMPLETO 🆕 **✅ COMPLETADA**

### COMANDO 3.5.1 - Dashboard Administrativo
```
@Claude: "Create a complete admin dashboard with real-time statistics, modern dark theme interface with responsive design, and professional layout using Tailwind CSS and Alpine.js"
```

**✅ IMPLEMENTADO COMPLETAMENTE:**
- Dashboard responsivo com métricas em tempo real
- Cards com gradientes para estatísticas principais
- Interface dark theme profissional
- Navigation sidebar expansível
- FontAwesome icons integrados
- Mobile-first responsive design

### COMANDO 3.5.2 - Gestão Completa de Vendedores
```
@Claude: "Implement complete seller management system with CRUD operations, approval/rejection workflow, commission management, search and filters, detailed seller profiles with modals for admin actions"
```

**✅ IMPLEMENTADO COMPLETAMENTE:**
- Lista de vendedores com filtros avançados
- Sistema de busca por nome/email/empresa
- Workflow completo de aprovação/rejeição
- Tracking temporal de ações administrativas
- Gestão individual de comissões
- Suspensão e reativação de contas
- Modals para ações administrativas
- Campos de rejection tracking (rejected_at, rejected_by, approved_by)

### COMANDO 3.5.3 - Sistema de Testes Robusto
```
@Claude: "Create comprehensive test suite for all admin functionality with 100% coverage for admin dashboard and seller management features"
```

**✅ IMPLEMENTADO COMPLETAMENTE:**
- AdminDashboardTest: 5/5 passing (100%)
- AdminSellerManagementTest: 10/10 passing (100%)
- MiddlewareTest: Admin section 100% passing
- Total: 18/18 admin tests passing

**✅ TESTE 3.5 - VERIFICAÇÃO COMPLETA:**
```bash
# Executar testes do sistema admin
php artisan test --filter=Admin
# Resultado esperado: 18/18 passing (100%)

# Acessar dashboard admin
http://localhost:8000/admin/dashboard
# Verificar: estatísticas, layout responsivo, navigation

# Testar gestão de vendedores
http://localhost:8000/admin/sellers
# Verificar: lista, filtros, busca, aprovação/rejeição
```

---

## 🛍️ FASE 4: ÁREA DO VENDEDOR (Dia 3-4)

### COMANDO 4.1 - Dashboard do Vendedor
```
@Claude: "Create seller dashboard with: sales metrics, pending orders count, product views, revenue chart using Alpine.js, and quick actions menu. Use Tailwind for responsive design"
```

**✅ TESTE 4.1:**
```bash
# Login como seller aprovado
# Acessar /seller/dashboard
# Ver métricas e gráficos
```

### COMANDO 4.2 - CRUD de Produtos
```
@Claude: "Create complete product CRUD for sellers: create form with multiple image upload (max 5), price, stock, variations, category selection. Include image preview with Alpine.js"
```

**✅ TESTE 4.2:**
- Criar produto com imagens
- Editar produto
- Soft delete produto
- Verificar imagens salvas em storage/app/public/products

### COMANDO 4.3 - Configurar Opções de Entrega
```
@Claude: "Create shipping options management for sellers: CRUD for shipping methods (fixed price, free shipping, pickup, negotiate via WhatsApp). Include default templates"
```

**✅ TESTE 4.3:**
- Adicionar frete fixo R$ 20
- Adicionar retirada grátis
- Ver opções no produto

---

## 💳 FASE 5: INTEGRAÇÃO MERCADO PAGO (Dia 4-5)

### COMANDO 5.1 - OAuth do Mercado Pago
```
@Claude: "Implement Mercado Pago OAuth flow for sellers: connect button, OAuth redirect, callback handling, save encrypted tokens, show connection status in seller settings"
```

**✅ TESTE 5.1:**
```bash
# Usar credenciais de teste do MP
# Seller conecta conta
# Verificar tokens salvos no banco
```

### COMANDO 5.2 - Serviço de Pagamento
```
@Claude: "Create MercadoPagoService for payment processing with automatic split: handle PIX (priority), credit card, and boleto. Include split calculation based on seller commission rate"
```

**✅ TESTE 5.2:**
```php
// Tinker
$service = new App\Services\MercadoPagoService();
$service->testConnection();
```

### COMANDO 5.3 - Webhook Handler
```
@Claude: "Create webhook endpoint for Mercado Pago payment notifications: verify signature, update order status, notify sellers, trigger stock decrease"
```

**✅ TESTE 5.3:**
```bash
# Usar ngrok para teste local
ngrok http 8000
# Configurar webhook URL no MP
# Fazer pagamento teste
```

---

## 🛒 FASE 6: LOJA PÚBLICA (Dia 5-6)

### COMANDO 6.1 - Home e Catálogo
```
@Claude: "Create public shop homepage with: featured products grid, categories menu, search bar with Alpine.js autocomplete, responsive product cards with Tailwind"
```

**✅ TESTE 6.1:**
- Acessar home
- Ver produtos
- Testar busca
- Responsivo mobile

### COMANDO 6.2 - Página do Produto
```
@Claude: "Create product detail page with: image gallery, price, variations selector, shipping options display, add to cart with Alpine.js, seller information"
```

**✅ TESTE 6.2:**
- Ver detalhes do produto
- Selecionar variação
- Ver opções de frete
- Adicionar ao carrinho

### COMANDO 6.3 - Carrinho de Compras
```
@Claude: "Create shopping cart with: items grouped by seller, quantity update, shipping selection per seller, order summary, responsive design"
```

**✅ TESTE 6.3:**
- Adicionar produtos de 2 vendedores
- Alterar quantidades
- Escolher frete diferente para cada
- Ver total calculado

---

## 💰 FASE 7: CHECKOUT E PAGAMENTO (Dia 6-7)

### COMANDO 7.1 - Fluxo de Checkout
```
@Claude: "Create checkout flow: customer data form, address input, payment method selection (PIX highlighted with 5% discount), order review, process payment with split"
```

**✅ TESTE 7.1:**
- Preencher dados
- Escolher PIX
- Ver desconto aplicado
- Confirmar pedido

### COMANDO 7.2 - Tela de Pagamento PIX
```
@Claude: "Create PIX payment screen with: QR Code display, copy-paste code, payment instructions, real-time status check via AJAX, auto-redirect on confirmation"
```

**✅ TESTE 7.2:**
- Ver QR Code
- Copiar código PIX
- Simular pagamento (sandbox MP)
- Ver redirecionamento automático

### COMANDO 7.3 - Confirmação e Notificações
```
@Claude: "Create order confirmation page and email notifications: customer receipt, seller new order alert, admin dashboard update. Use Laravel queues"
```

**✅ TESTE 7.3:**
```bash
php artisan queue:work
# Fazer pedido
# Verificar emails enviados
# Ver notificações no dashboard
```

---

## 👨‍💼 FASE 8: PAINEL ADMINISTRATIVO (Dia 7-8)

### COMANDO 8.1 - Dashboard Admin
```
@Claude: "Create admin dashboard with: sales metrics (daily/weekly/monthly), seller performance ranking, pending approvals count, recent orders table, charts with Alpine.js"
```

**✅ TESTE 8.1:**
- Login como admin
- Ver todas métricas
- Gráficos funcionando
- Dados em tempo real

### COMANDO 8.2 - Gestão de Vendedores
```
@Claude: "Create seller management interface: approval queue with document view, approve/reject with reason, commission rate setting, seller list with filters and actions"
```

**✅ TESTE 8.2:**
- Aprovar seller pendente
- Definir comissão 15%
- Suspender seller
- Ver histórico

### COMANDO 8.3 - Configurações do Marketplace
```
@Claude: "Create marketplace settings: default commission rate, category management CRUD, payment settings, email templates, general settings form"
```

**✅ TESTE 8.3:**
- Alterar comissão padrão
- Criar nova categoria
- Salvar configurações
- Ver mudanças aplicadas

---

## 🧪 FASE 9: TESTES E OTIMIZAÇÃO (Dia 8-9)

### COMANDO 9.1 - Testes de Feature
```
@Claude: "Create Pest/PHPUnit tests for critical features: user registration, seller approval, product creation, cart operations, payment processing, order split"
```

**✅ TESTE 9.1:**
```bash
php artisan test
php artisan test --filter=PaymentTest
php artisan test --coverage
```

### COMANDO 9.2 - Otimização de Queries
```
@Claude: "Optimize database queries: add missing indexes, implement eager loading where needed, add query caching for product listings, optimize N+1 problems"
```

**✅ TESTE 9.2:**
```bash
# Laravel Debugbar para ver queries
composer require barryvdh/laravel-debugbar --dev
# Verificar queries na home
# Deve ter menos de 10 queries
```

### COMANDO 9.3 - Segurança
```
@Claude: "Security review: implement rate limiting, add CSRF protection verification, sanitize all inputs, secure file uploads, add security headers"
```

**✅ TESTE 9.3:**
```bash
# Testar rate limiting
# Tentar SQL injection
# Testar XSS
# Upload arquivo malicioso
```

---

## 🚀 FASE 10: DEPLOY PREPARATION (Dia 9-10)

### COMANDO 10.1 - Configurações de Produção
```
@Claude: "Prepare for production: create .env.production example, optimize autoload, cache config and routes, compile assets, create deployment checklist"
```

**✅ TESTE 10.1:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
php artisan optimize
```

### COMANDO 10.2 - Documentação Final
```
@Claude: "Create README.md with: installation instructions, requirements, configuration guide, API documentation, troubleshooting section"
```

### COMANDO 10.3 - Backup e Versionamento
```
@Claude: "Create database backup script, implement version tags, create CHANGELOG.md, set up GitHub repository structure with proper .gitignore"
```

---

## 📊 CHECKPOINTS DE PROGRESSO

### ✅ Após FASE 1 (Estrutura Base) - CONCLUÍDA 28/08/2025
- [x] Laravel 12.26.3 instalado
- [x] Breeze configurado
- [x] Tailwind + Vite funcionando
- [x] Middlewares registrados

### ✅ Após FASE 2 (Banco de Dados) - CONCLUÍDA 28/08/2025
- [x] Todas as 17 tabelas criadas
- [x] MySQL configurado
- [x] Relationships implementados
- [x] Dicionário de dados criado
- [x] Seeders funcionando ✅ CONCLUÍDO

### ✅ Após FASE 3 (Autenticação) - CONCLUÍDA 28/08/2025
- [x] Multi-auth implementado
- [x] Onboarding de vendedores completo
- [x] Admin approval interface
- [x] Views de onboarding

### 🟢 Após FASE 4 (Área Seller) - 85% CONCLUÍDA
- [x] Seller dashboard route
- [x] Seller dashboard controller com redirecionamento por status
- [x] Views de pending/rejected implementadas
- [x] Admin dashboard com métricas implementado
- [x] Admin gestão de vendedores implementada
- [ ] CRUD de produtos ⚠️ ÚNICA PENDÊNCIA CRÍTICA
- [ ] Configuração de frete

### ❌ Após FASE 5 (Mercado Pago) - NÃO INICIADA
- [ ] OAuth funcionando
- [ ] Pagamento PIX testado
- [ ] Split confirmado

### ❌ Após FASE 7 (Checkout) - NÃO INICIADA
- [ ] Fluxo completo testado
- [ ] Pagamento aprovado
- [ ] Notificações enviadas

### 🟡 Progresso Global - 85% ESTRUTURA COMPLETA
- [x] Todos models críticos implementados (12/12) ✅
- [x] Controllers principais implementados (8+/12) ✅
- [x] Views administrativas implementadas ✅
- [x] Testes estruturais passando (81% - excelente) ✅
- [ ] CRUD de produtos (ÚNICA PENDÊNCIA CRÍTICA)
- [ ] Loja pública completa
- [ ] Integração de pagamentos
- [ ] Performance otimizada
- [ ] Pronto para deploy

---

## 🔥 COMANDOS RÁPIDOS PARA PROBLEMAS COMUNS

### Erro de Migration
```
@Claude: "Fix migration error: [cole o erro]. Adjust the migration and provide rollback solution"
```

### Erro de Pagamento
```
@Claude: "Debug Mercado Pago payment error: [cole o log]. Check credentials and split configuration"
```

### Performance Lenta
```
@Claude: "Optimize this slow query: [cole a query]. Add indexes and implement caching"
```

### Layout Quebrado
```
@Claude: "Fix responsive layout issue in [página]. Ensure Tailwind classes are correct for all breakpoints"
```

---

## 📝 DICAS PARA USAR O CLAUDE CODE

### 1. Seja Específico
❌ "Create payment system"
✅ "Create PIX payment processing with Mercado Pago split for multiple sellers following PROJECT-SPECS.md"

### 2. Referencie Contexto
```
"Continue from the checkout implementation, now add real-time payment status checking"
```

### 3. Peça Testes
```
"After creating the feature, also create Pest tests for the main scenarios"
```

### 4. Corrija Incrementalmente
```
"The payment split is not calculating commission correctly. Commission should be 10% by default or use seller's custom rate"
```

### 5. Use o Modo Composer (Ctrl+I)
Para edições rápidas em múltiplos arquivos

---

## 🎯 CRONOGRAMA SUGERIDO

| Dia | Fases | Objetivo |
|-----|-------|----------|
| 1 | 1-2 | Estrutura e Banco |
| 2 | 3 | Autenticação |
| 3-4 | 4 | Área Vendedor |
| 4-5 | 5 | Mercado Pago |
| 5-6 | 6 | Loja Pública |
| 6-7 | 7 | Checkout |
| 7-8 | 8 | Admin |
| 8-9 | 9 | Testes |
| 9-10 | 10 | Deploy |

**Total: 10 dias para MVP completo**

---

## ⚡ MODO TURBO (3-4 dias)

Se precisar acelerar, peça ao Claude:
```
@Claude: "Implement phases 1-3 in a single batch with basic functionality, focusing on core features only"
```

---

## 🆘 SUPORTE

### Erro que o Claude não resolve?
1. Copie o erro completo
2. Peça: "Debug this error with alternative solutions: [erro]"
3. Se persistir, consulte documentação Laravel 12 ou Mercado Pago

### Performance ruim?
1. Use Laravel Debugbar
2. Peça: "Analyze and optimize performance bottlenecks shown in debugbar"

### Dúvida de negócio?
Sempre referencie: "According to PROJECT-SPECS.md, how should [feature] work?"

---

**LEMBRE-SE**: 
- Teste cada fase antes de prosseguir
- Commit no Git após cada fase completa
- Mantenha .env.example atualizado
- Documente mudanças importantes

✅ **PRONTO PARA COMEÇAR!**