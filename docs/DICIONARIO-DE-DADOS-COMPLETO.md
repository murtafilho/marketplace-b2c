# 📊 Dicionário de Dados Completo - Marketplace B2C

**Data de Criação**: 31/08/2025  
**Última Atualização**: 31/08/2025  
**Versão**: 1.0  

---

## 🎯 Objetivo

Este documento define a estrutura padronizada de todos os campos do banco de dados do Marketplace B2C, servindo como referência única para evitar inconsistências na nomenclatura durante o desenvolvimento.

---

## 📋 Convenções Gerais

### Nomenclatura
- **Tabelas**: `snake_case` no plural (ex: `cart_items`, `seller_profiles`)
- **Campos**: `snake_case` no singular (ex: `user_id`, `created_at`)
- **Chaves Primárias**: sempre `id` (BIGINT UNSIGNED AUTO_INCREMENT)
- **Chaves Estrangeiras**: `{tabela_singular}_id` (ex: `user_id`, `product_id`)
- **Timestamps**: sempre `created_at` e `updated_at` (TIMESTAMP NULL)
- **Soft Deletes**: `deleted_at` (TIMESTAMP NULL)

### Tipos de Dados Padronizados
- **IDs**: `BIGINT UNSIGNED`
- **Strings Curtas**: `VARCHAR(255)`
- **Textos Longos**: `TEXT` ou `LONGTEXT`
- **Preços/Valores**: `DECIMAL(10,2)`
- **Percentuais**: `DECIMAL(5,2)`
- **Booleanos**: `TINYINT(1)` com DEFAULT
- **JSONs**: `JSON`
- **Timestamps**: `TIMESTAMP`
- **Enums**: `VARCHAR` com valores específicos

---

## 🗃️ TABELAS DO SISTEMA

### 1. **users** - Usuários do Sistema
**Descrição**: Tabela principal de usuários (compradores, vendedores e administradores)

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(255) | NÃO | - | Nome completo do usuário |
| `email` | VARCHAR(255) | NÃO | - | Email único para login |
| `email_verified_at` | TIMESTAMP | SIM | NULL | Data de verificação do email |
| `password` | VARCHAR(255) | NÃO | - | Senha hasheada |
| `role` | VARCHAR(20) | NÃO | 'customer' | Papel: admin, seller, customer |
| `phone` | VARCHAR(20) | SIM | NULL | Telefone de contato |
| `is_active` | TINYINT(1) | NÃO | 1 | Status ativo/inativo |
| `remember_token` | VARCHAR(100) | SIM | NULL | Token para "lembrar login" |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |
| `deleted_at` | TIMESTAMP | SIM | NULL | Data de exclusão (soft delete) |

**Índices**:
- PRIMARY KEY: `id`
- UNIQUE: `email`
- INDEX: `role`, `is_active`, `deleted_at`

**Relacionamentos**:
- 1:1 com `seller_profiles` (se role = 'seller')
- 1:N com `products` (como seller_id)
- 1:N com `orders` (como user_id)
- 1:N com `carts` (como user_id)

---

### 2. **seller_profiles** - Perfis de Vendedores
**Descrição**: Dados adicionais específicos dos vendedores para aprovação e gestão

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `user_id` | BIGINT UNSIGNED | NÃO | - | FK para users |
| `document_type` | VARCHAR(10) | SIM | NULL | Tipo: CPF ou CNPJ |
| `document_number` | VARCHAR(20) | SIM | NULL | Número do documento |
| `company_name` | VARCHAR(255) | SIM | NULL | Nome da empresa (se CNPJ) |
| `address_proof_path` | VARCHAR(500) | SIM | NULL | Caminho do comprovante de endereço |
| `identity_proof_path` | VARCHAR(500) | SIM | NULL | Caminho do documento de identidade |
| `phone` | VARCHAR(20) | SIM | NULL | Telefone comercial |
| `address` | TEXT | SIM | NULL | Endereço completo |
| `city` | VARCHAR(100) | SIM | NULL | Cidade |
| `state` | VARCHAR(2) | SIM | NULL | Estado (sigla) |
| `postal_code` | VARCHAR(10) | SIM | NULL | CEP |
| `bank_name` | VARCHAR(100) | SIM | NULL | Nome do banco |
| `bank_agency` | VARCHAR(10) | SIM | NULL | Agência bancária |
| `bank_account` | VARCHAR(20) | SIM | NULL | Conta bancária |
| `status` | VARCHAR(20) | NÃO | 'pending' | pending, approved, rejected, suspended |
| `rejection_reason` | TEXT | SIM | NULL | Motivo da rejeição |
| `commission_rate` | DECIMAL(5,2) | NÃO | 10.00 | Taxa de comissão (%) |
| `product_limit` | INT | NÃO | 100 | Limite de produtos |
| `mp_access_token` | VARCHAR(500) | SIM | NULL | Token do Mercado Pago |
| `mp_user_id` | VARCHAR(50) | SIM | NULL | ID do usuário no MP |
| `mp_connected` | TINYINT(1) | NÃO | 0 | Status conexão MP |
| `approved_at` | TIMESTAMP | SIM | NULL | Data de aprovação |
| `rejected_at` | TIMESTAMP | SIM | NULL | Data de rejeição |
| `submitted_at` | TIMESTAMP | SIM | NULL | Data de submissão |
| `rejected_by` | BIGINT UNSIGNED | SIM | NULL | FK para users (admin que rejeitou) |
| `approved_by` | BIGINT UNSIGNED | SIM | NULL | FK para users (admin que aprovou) |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

**Índices**:
- PRIMARY KEY: `id`
- UNIQUE: `user_id`, `document_number`
- INDEX: `status`

---

### 3. **products** - Produtos
**Descrição**: Catálogo de produtos dos vendedores

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `seller_id` | BIGINT UNSIGNED | NÃO | - | FK para users (vendedor) |
| `category_id` | BIGINT UNSIGNED | NÃO | - | FK para categories |
| `name` | VARCHAR(255) | NÃO | - | Nome do produto |
| `slug` | VARCHAR(255) | NÃO | - | URL amigável (único) |
| `description` | LONGTEXT | NÃO | - | Descrição completa |
| `short_description` | TEXT | SIM | NULL | Descrição resumida |
| `price` | DECIMAL(10,2) | NÃO | - | Preço de venda |
| `compare_at_price` | DECIMAL(10,2) | SIM | NULL | Preço "de" para promoções |
| `cost` | DECIMAL(10,2) | SIM | NULL | Custo do produto |
| `stock_quantity` | INT | NÃO | 0 | Quantidade em estoque |
| `stock_status` | VARCHAR(20) | NÃO | 'in_stock' | in_stock, out_of_stock, low_stock |
| `sku` | VARCHAR(100) | SIM | NULL | Código do produto |
| `barcode` | VARCHAR(50) | SIM | NULL | Código de barras |
| `weight` | DECIMAL(8,3) | SIM | NULL | Peso em KG |
| `length` | DECIMAL(8,2) | SIM | NULL | Comprimento em CM |
| `width` | DECIMAL(8,2) | SIM | NULL | Largura em CM |
| `height` | DECIMAL(8,2) | SIM | NULL | Altura em CM |
| `status` | VARCHAR(20) | NÃO | 'draft' | draft, published, pending, rejected |
| `featured` | TINYINT(1) | NÃO | 0 | Produto em destaque |
| `digital` | TINYINT(1) | NÃO | 0 | Produto digital |
| `downloadable_files` | JSON | SIM | NULL | Arquivos para download |
| `meta_title` | VARCHAR(255) | SIM | NULL | Título SEO |
| `meta_description` | TEXT | SIM | NULL | Descrição SEO |
| `meta_keywords` | TEXT | SIM | NULL | Palavras-chave SEO |
| `views_count` | INT | NÃO | 0 | Contador de visualizações |
| `sales_count` | INT | NÃO | 0 | Contador de vendas |
| `rating_average` | DECIMAL(3,2) | SIM | NULL | Média de avaliações |
| `rating_count` | INT | NÃO | 0 | Quantidade de avaliações |
| `published_at` | TIMESTAMP | SIM | NULL | Data de publicação |
| `brand` | VARCHAR(100) | SIM | NULL | Marca do produto |
| `model` | VARCHAR(100) | SIM | NULL | Modelo do produto |
| `warranty_months` | INT | SIM | NULL | Garantia em meses |
| `tags` | JSON | SIM | NULL | Tags do produto |
| `attributes` | JSON | SIM | NULL | Atributos específicos |
| `dimensions` | JSON | SIM | NULL | Dimensões extras |
| `shipping_class` | VARCHAR(50) | SIM | NULL | Classe de frete |
| `deleted_at` | TIMESTAMP | SIM | NULL | Data de exclusão (soft delete) |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

**Índices**:
- PRIMARY KEY: `id`
- UNIQUE: `slug`
- INDEX: `seller_id`, `category_id`, `status`, `stock_quantity`, `featured`, `created_at`

---

### 4. **product_images** - Imagens dos Produtos
**Descrição**: Gerenciamento de imagens dos produtos

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `product_id` | BIGINT UNSIGNED | NÃO | - | FK para products |
| `original_name` | VARCHAR(255) | NÃO | - | Nome original do arquivo |
| `file_name` | VARCHAR(255) | NÃO | - | Nome do arquivo salvo |
| `file_path` | VARCHAR(500) | NÃO | - | Caminho completo do arquivo |
| `thumbnail_path` | VARCHAR(500) | SIM | NULL | Caminho da miniatura |
| `mime_type` | VARCHAR(100) | NÃO | - | Tipo MIME do arquivo |
| `file_size` | BIGINT | NÃO | - | Tamanho em bytes |
| `width` | INT | SIM | NULL | Largura da imagem |
| `height` | INT | SIM | NULL | Altura da imagem |
| `alt_text` | VARCHAR(255) | SIM | NULL | Texto alternativo |
| `is_primary` | TINYINT(1) | NÃO | 0 | Imagem principal |
| `sort_order` | INT | NÃO | 0 | Ordem de exibição |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 5. **product_variations** - Variações dos Produtos
**Descrição**: Variações de produtos (tamanho, cor, etc.)

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `product_id` | BIGINT UNSIGNED | NÃO | - | FK para products |
| `name` | VARCHAR(100) | NÃO | - | Nome da variação (Tamanho, Cor) |
| `value` | VARCHAR(100) | NÃO | - | Valor da variação (M, Azul) |
| `price_adjustment` | DECIMAL(10,2) | NÃO | 0.00 | Ajuste no preço |
| `stock_quantity` | INT | NÃO | 0 | Estoque específico |
| `sku_suffix` | VARCHAR(50) | SIM | NULL | Sufixo para o SKU |
| `weight_adjustment` | DECIMAL(8,3) | NÃO | 0.00 | Ajuste no peso |
| `is_active` | TINYINT(1) | NÃO | 1 | Variação ativa |
| `sort_order` | INT | NÃO | 0 | Ordem de exibição |
| `meta_data` | JSON | SIM | NULL | Dados adicionais |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 6. **categories** - Categorias
**Descrição**: Categorias e subcategorias de produtos

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(255) | NÃO | - | Nome da categoria |
| `slug` | VARCHAR(255) | NÃO | - | URL amigável (único) |
| `icon` | VARCHAR(100) | SIM | NULL | Ícone da categoria |
| `description` | TEXT | SIM | NULL | Descrição da categoria |
| `image_path` | VARCHAR(500) | SIM | NULL | Imagem da categoria |
| `parent_id` | BIGINT UNSIGNED | SIM | NULL | FK para categories (autocategoria) |
| `is_active` | TINYINT(1) | NÃO | 1 | Categoria ativa |
| `sort_order` | INT | NÃO | 0 | Ordem de exibição |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 7. **carts** - Carrinhos
**Descrição**: Carrinhos de compras (sessão e usuários logados)

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `user_id` | BIGINT UNSIGNED | SIM | NULL | FK para users (se logado) |
| `session_id` | VARCHAR(255) | SIM | NULL | ID da sessão (se não logado) |
| `total_amount` | DECIMAL(10,2) | NÃO | 0.00 | Total do carrinho |
| `total_items` | INT | NÃO | 0 | Total de itens |
| `shipping_data` | JSON | SIM | NULL | Dados de frete calculados |
| `coupon_data` | JSON | SIM | NULL | Dados de cupons aplicados |
| `last_activity` | TIMESTAMP | NÃO | CURRENT_TIMESTAMP | Última atividade |
| `expires_at` | TIMESTAMP | SIM | NULL | Data de expiração |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

**Índices**:
- INDEX: `user_id`, `session_id`, `last_activity`, `expires_at`

---

### 8. **cart_items** - Itens do Carrinho
**Descrição**: Itens individuais dentro dos carrinhos

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `cart_id` | BIGINT UNSIGNED | NÃO | - | FK para carts |
| `product_id` | BIGINT UNSIGNED | NÃO | - | FK para products |
| `product_variation_id` | BIGINT UNSIGNED | SIM | NULL | FK para product_variations |
| `quantity` | INT | NÃO | - | Quantidade |
| `unit_price` | DECIMAL(10,2) | NÃO | - | Preço unitário |
| `total_price` | DECIMAL(10,2) | NÃO | - | Preço total (unit_price * quantity) |
| `product_snapshot` | JSON | SIM | NULL | Snapshot do produto na data |
| `variation_snapshot` | JSON | SIM | NULL | Snapshot da variação na data |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 9. **orders** - Pedidos
**Descrição**: Pedidos principais dos compradores

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `order_number` | VARCHAR(50) | NÃO | - | Número único do pedido |
| `user_id` | BIGINT UNSIGNED | NÃO | - | FK para users (comprador) |
| `status` | VARCHAR(20) | NÃO | 'pending' | pending, confirmed, processing, shipped, delivered, cancelled |
| `subtotal` | DECIMAL(10,2) | NÃO | - | Subtotal dos produtos |
| `shipping_total` | DECIMAL(10,2) | NÃO | 0.00 | Total do frete |
| `tax_total` | DECIMAL(10,2) | NÃO | 0.00 | Total de impostos |
| `discount_total` | DECIMAL(10,2) | NÃO | 0.00 | Total de descontos |
| `total` | DECIMAL(10,2) | NÃO | - | Total geral |
| `currency` | VARCHAR(3) | NÃO | 'BRL' | Moeda |
| `payment_status` | VARCHAR(20) | NÃO | 'pending' | pending, paid, failed, refunded |
| `payment_method` | VARCHAR(50) | SIM | NULL | Método de pagamento |
| `billing_address` | JSON | NÃO | - | Endereço de cobrança |
| `shipping_address` | JSON | SIM | NULL | Endereço de entrega |
| `customer_notes` | TEXT | SIM | NULL | Observações do cliente |
| `admin_notes` | TEXT | SIM | NULL | Observações administrativas |
| `coupon_data` | JSON | SIM | NULL | Dados de cupons aplicados |
| `mp_payment_id` | VARCHAR(100) | SIM | NULL | ID do pagamento no MP |
| `payment_preference_id` | VARCHAR(100) | SIM | NULL | ID da preferência no MP |
| `payment_data` | JSON | SIM | NULL | Dados completos do pagamento |
| `confirmed_at` | TIMESTAMP | SIM | NULL | Data de confirmação |
| `shipped_at` | TIMESTAMP | SIM | NULL | Data de envio |
| `delivered_at` | TIMESTAMP | SIM | NULL | Data de entrega |
| `deleted_at` | TIMESTAMP | SIM | NULL | Data de exclusão (soft delete) |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

**Índices**:
- UNIQUE: `order_number`
- INDEX: `user_id`, `status`, `payment_status`, `mp_payment_id`, `created_at`

---

### 10. **sub_orders** - Sub-pedidos
**Descrição**: Divisão dos pedidos por vendedor para facilitar gestão e entrega

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `sub_order_number` | VARCHAR(50) | NÃO | - | Número único do sub-pedido |
| `order_id` | BIGINT UNSIGNED | NÃO | - | FK para orders |
| `seller_id` | BIGINT UNSIGNED | NÃO | - | FK para users (vendedor) |
| `status` | VARCHAR(20) | NÃO | 'pending' | pending, confirmed, processing, shipped, delivered, cancelled |
| `subtotal` | DECIMAL(10,2) | NÃO | - | Subtotal dos produtos do vendedor |
| `shipping_cost` | DECIMAL(10,2) | NÃO | 0.00 | Custo de frete do vendedor |
| `seller_amount` | DECIMAL(10,2) | NÃO | - | Valor que o vendedor recebe |
| `commission_amount` | DECIMAL(10,2) | NÃO | - | Valor da comissão |
| `commission_rate` | DECIMAL(5,2) | NÃO | - | Taxa de comissão aplicada |
| `shipping_method` | VARCHAR(100) | SIM | NULL | Método de entrega |
| `tracking_info` | JSON | SIM | NULL | Informações de rastreamento |
| `seller_notes` | TEXT | SIM | NULL | Observações do vendedor |
| `shipped_at` | TIMESTAMP | SIM | NULL | Data de envio |
| `delivered_at` | TIMESTAMP | SIM | NULL | Data de entrega |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 11. **order_items** - Itens dos Pedidos
**Descrição**: Produtos específicos dentro dos pedidos

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `order_id` | BIGINT UNSIGNED | NÃO | - | FK para orders |
| `sub_order_id` | BIGINT UNSIGNED | NÃO | - | FK para sub_orders |
| `product_id` | BIGINT UNSIGNED | NÃO | - | FK para products |
| `product_variation_id` | BIGINT UNSIGNED | SIM | NULL | FK para product_variations |
| `product_name` | VARCHAR(255) | NÃO | - | Nome do produto na data |
| `product_sku` | VARCHAR(100) | SIM | NULL | SKU do produto na data |
| `quantity` | INT | NÃO | - | Quantidade comprada |
| `unit_price` | DECIMAL(10,2) | NÃO | - | Preço unitário na data |
| `total_price` | DECIMAL(10,2) | NÃO | - | Preço total do item |
| `product_snapshot` | JSON | SIM | NULL | Snapshot do produto completo |
| `variation_snapshot` | JSON | SIM | NULL | Snapshot da variação |
| `commission_rate` | DECIMAL(5,2) | NÃO | - | Taxa de comissão |
| `commission_amount` | DECIMAL(10,2) | NÃO | - | Valor da comissão |
| `seller_amount` | DECIMAL(10,2) | NÃO | - | Valor para o vendedor |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 12. **transactions** - Transações Financeiras
**Descrição**: Registro de todas as transações financeiras e splits

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `order_id` | BIGINT UNSIGNED | NÃO | - | FK para orders |
| `seller_id` | BIGINT UNSIGNED | NÃO | - | FK para users (vendedor) |
| `mp_payment_id` | VARCHAR(100) | SIM | NULL | ID do pagamento no MP |
| `type` | VARCHAR(20) | NÃO | 'payment' | payment, refund, commission, withdrawal |
| `status` | VARCHAR(20) | NÃO | 'pending' | pending, completed, failed, cancelled |
| `amount` | DECIMAL(10,2) | NÃO | - | Valor da transação |
| `commission_rate` | DECIMAL(5,2) | NÃO | - | Taxa de comissão |
| `commission_amount` | DECIMAL(10,2) | NÃO | - | Valor da comissão |
| `seller_amount` | DECIMAL(10,2) | NÃO | - | Valor para o vendedor |
| `mp_collector_id` | VARCHAR(100) | SIM | NULL | ID do coletor no MP |
| `mp_response` | JSON | SIM | NULL | Resposta completa do MP |
| `notes` | TEXT | SIM | NULL | Observações |
| `processed_at` | TIMESTAMP | SIM | NULL | Data de processamento |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

**Índices**:
- INDEX: `order_id`, `seller_id`, `mp_payment_id`, `status`, `processed_at`

---

### 13. **seller_shipping_options** - Opções de Frete dos Vendedores
**Descrição**: Métodos de entrega configurados pelos vendedores

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `seller_id` | BIGINT UNSIGNED | NÃO | - | FK para users (vendedor) |
| `name` | VARCHAR(100) | NÃO | - | Nome da opção (Frete Grátis) |
| `type` | VARCHAR(20) | NÃO | - | fixed, percentage, weight, custom |
| `value` | DECIMAL(10,2) | NÃO | 0.00 | Valor base |
| `min_order_amount` | DECIMAL(10,2) | SIM | NULL | Valor mínimo para aplicar |
| `max_weight` | DECIMAL(8,3) | SIM | NULL | Peso máximo |
| `delivery_days` | INT | SIM | NULL | Dias estimados de entrega |
| `description` | TEXT | SIM | NULL | Descrição da opção |
| `is_active` | TINYINT(1) | NÃO | 1 | Opção ativa |
| `sort_order` | INT | NÃO | 0 | Ordem de exibição |
| `regions` | JSON | SIM | NULL | Regiões atendidas |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

### 14. **media** - Biblioteca de Mídia
**Descrição**: Sistema de gestão de arquivos usando Spatie Media Library

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `model_type` | VARCHAR(255) | NÃO | - | Classe do modelo |
| `model_id` | BIGINT UNSIGNED | NÃO | - | ID do modelo |
| `uuid` | CHAR(36) | SIM | NULL | UUID único |
| `collection_name` | VARCHAR(255) | NÃO | - | Nome da coleção |
| `name` | VARCHAR(255) | NÃO | - | Nome do arquivo |
| `file_name` | VARCHAR(255) | NÃO | - | Nome do arquivo no disco |
| `mime_type` | VARCHAR(255) | SIM | NULL | Tipo MIME |
| `disk` | VARCHAR(255) | NÃO | - | Disco de armazenamento |
| `conversions_disk` | VARCHAR(255) | SIM | NULL | Disco das conversões |
| `size` | BIGINT UNSIGNED | NÃO | - | Tamanho em bytes |
| `manipulations` | JSON | NÃO | - | Manipulações aplicadas |
| `custom_properties` | JSON | NÃO | - | Propriedades customizadas |
| `generated_conversions` | JSON | NÃO | - | Conversões geradas |
| `responsive_images` | JSON | NÃO | - | Imagens responsivas |
| `order_column` | INT | SIM | NULL | Ordem na coleção |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

**Índices**:
- INDEX: `model_type`, `model_id`, `uuid`, `order_column`

---

### 15. **test_uploads** - Tabela de Teste (Temporária)
**Descrição**: Tabela temporária para testes de upload - **DEVE SER REMOVIDA EM PRODUÇÃO**

| Campo | Tipo | Nulo | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | BIGINT UNSIGNED | NÃO | AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(255) | NÃO | - | Nome do teste |
| `description` | TEXT | SIM | NULL | Descrição do teste |
| `created_at` | TIMESTAMP | SIM | NULL | Data de criação |
| `updated_at` | TIMESTAMP | SIM | NULL | Data de atualização |

---

## 🔗 RELACIONAMENTOS PRINCIPAIS

### Relacionamentos 1:1
- `users.id` → `seller_profiles.user_id` (se role = 'seller')

### Relacionamentos 1:N
- `users.id` → `products.seller_id`
- `users.id` → `orders.user_id`
- `users.id` → `carts.user_id`
- `users.id` → `transactions.seller_id`
- `categories.id` → `products.category_id`
- `categories.id` → `categories.parent_id` (auto-relacionamento)
- `products.id` → `product_images.product_id`
- `products.id` → `product_variations.product_id`
- `carts.id` → `cart_items.cart_id`
- `orders.id` → `sub_orders.order_id`
- `orders.id` → `order_items.order_id`
- `orders.id` → `transactions.order_id`
- `sub_orders.id` → `order_items.sub_order_id`

### Relacionamentos N:N
- `cart_items.product_id` → `products.id`
- `cart_items.product_variation_id` → `product_variations.id`
- `order_items.product_id` → `products.id`
- `order_items.product_variation_id` → `product_variations.id`

---

## 📊 ENUMS E VALORES PADRONIZADOS

### Status dos Usuários (`users.role`)
- `admin` - Administrador do sistema
- `seller` - Vendedor
- `customer` - Comprador

### Status dos Vendedores (`seller_profiles.status`)
- `pending` - Aguardando aprovação
- `approved` - Aprovado e ativo
- `rejected` - Rejeitado
- `suspended` - Suspenso temporariamente

### Status dos Produtos (`products.status`)
- `draft` - Rascunho (não visível)
- `published` - Publicado e visível
- `pending` - Aguardando aprovação
- `rejected` - Rejeitado pelo admin

### Status de Estoque (`products.stock_status`)
- `in_stock` - Em estoque
- `out_of_stock` - Fora de estoque
- `low_stock` - Estoque baixo

### Status dos Pedidos (`orders.status`, `sub_orders.status`)
- `pending` - Aguardando confirmação
- `confirmed` - Confirmado
- `processing` - Em processamento
- `shipped` - Enviado
- `delivered` - Entregue
- `cancelled` - Cancelado

### Status de Pagamento (`orders.payment_status`)
- `pending` - Aguardando pagamento
- `paid` - Pago
- `failed` - Falhou
- `refunded` - Estornado

### Tipos de Transação (`transactions.type`)
- `payment` - Pagamento
- `refund` - Estorno
- `commission` - Comissão
- `withdrawal` - Saque

### Status de Transação (`transactions.status`)
- `pending` - Aguardando processamento
- `completed` - Concluída
- `failed` - Falhou
- `cancelled` - Cancelada

---

## 🛡️ VALIDAÇÕES E CONSTRAINTS

### Validações Obrigatórias
1. **Emails únicos** em `users.email`
2. **Slugs únicos** em `products.slug` e `categories.slug`
3. **Números de pedido únicos** em `orders.order_number` e `sub_orders.sub_order_number`
4. **Documentos únicos** em `seller_profiles.document_number`
5. **Valores decimais positivos** para preços e quantidades
6. **Foreign Keys válidas** para todas as relações

### Constraints Recomendadas
1. **CHECK** para roles válidas em `users.role`
2. **CHECK** para status válidos em todas as tabelas
3. **CHECK** para valores positivos em campos de preço/quantidade
4. **CHECK** para percentuais entre 0 e 100

---

## 📝 OBSERVAÇÕES IMPORTANTES

### Para Desenvolvedores
1. **SEMPRE usar os nomes exatos** definidos neste dicionário
2. **Verificar tipos de dados** antes de criar novos campos
3. **Seguir convenções de nomenclatura** para consistency
4. **Adicionar índices** em campos usados em WHERE/JOIN/ORDER BY
5. **Usar soft deletes** em tabelas principais (users, products, orders)

### Para DBA
1. **Monitorar performance** de queries em tabelas grandes
2. **Configurar backup** especialmente para orders e transactions
3. **Implementar particionamento** se necessário para tabelas de logs
4. **Otimizar índices** baseado em queries reais

### Para QA
1. **Testar constraints** de foreign keys
2. **Validar enums** e valores permitidos
3. **Testar soft deletes** e recuperação
4. **Verificar integridade** dos relacionamentos

---

## 🔄 HISTÓRICO DE MUDANÇAS

| Data | Versão | Autor | Alterações |
|------|--------|-------|------------|
| 31/08/2025 | 1.0 | Claude Code | Criação inicial do dicionário |

---

**📌 Nota**: Este documento deve ser atualizado sempre que houver alterações na estrutura do banco de dados. Toda nova tabela ou campo deve seguir rigorosamente estas convenções.