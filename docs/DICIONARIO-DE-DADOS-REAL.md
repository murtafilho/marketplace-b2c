# 📊 DICIONÁRIO DE DADOS - CAMPOS REAIS
*Atualizado em: 30/08/2025*
*Baseado no banco de dados atual*

## 🚨 **IMPORTANTE**
Este documento contém os **campos exatos** das tabelas no banco. Use sempre estes nomes nos seeders e código!

---

## 👥 **TABELA: users**

| Campo | Tipo | Null | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | bigint unsigned | NO | | Chave primária |
| `name` | varchar(255) | NO | | Nome completo |
| `email` | varchar(255) | NO | | Email único |
| `email_verified_at` | timestamp | YES | | Data verificação email |
| `password` | varchar(255) | NO | | Senha hash |
| `role` | enum | NO | customer | admin/seller/customer |
| `phone` | varchar(255) | YES | | Telefone |
| `is_active` | tinyint(1) | NO | 1 | Usuário ativo |
| `remember_token` | varchar(100) | YES | | Token "lembrar" |
| `created_at` | timestamp | YES | | Data criação |
| `updated_at` | timestamp | YES | | Data atualização |
| `deleted_at` | timestamp | YES | | Soft delete |

---

## 🏪 **TABELA: seller_profiles**

| Campo | Tipo | Null | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | bigint unsigned | NO | | Chave primária |
| `user_id` | bigint unsigned | NO | | FK para users |
| `document_type` | varchar(10) | YES | | cpf/cnpj |
| `document_number` | varchar(20) | YES | | Número documento |
| **`company_name`** | varchar(255) | YES | | **Nome empresa** ⚠️ |
| `address_proof_path` | varchar(255) | YES | | Comprovante endereço |
| `identity_proof_path` | varchar(255) | YES | | Documento identidade |
| `phone` | varchar(20) | YES | | Telefone |
| `address` | varchar(255) | YES | | Endereço |
| `city` | varchar(100) | YES | | Cidade |
| `state` | varchar(2) | YES | | Estado (UF) |
| `postal_code` | varchar(10) | YES | | CEP |
| `bank_name` | varchar(100) | YES | | Banco |
| `bank_agency` | varchar(20) | YES | | Agência |
| `bank_account` | varchar(50) | YES | | Conta |
| `status` | enum | NO | pending | Status aprovação |
| `rejection_reason` | text | YES | | Motivo rejeição |
| **`commission_rate`** | decimal(5,2) | NO | 10.00 | **Taxa comissão** ⚠️ |
| `product_limit` | int | NO | 100 | Limite produtos |
| `mp_access_token` | varchar(500) | YES | | Token MercadoPago |
| `mp_user_id` | varchar(100) | YES | | ID MercadoPago |
| `mp_connected` | tinyint(1) | NO | 0 | MP conectado |
| `approved_at` | timestamp | YES | | Data aprovação |
| `rejected_at` | timestamp | YES | | Data rejeição |
| `submitted_at` | timestamp | YES | | Data submissão |
| `created_at` | timestamp | YES | | Data criação |
| `updated_at` | timestamp | YES | | Data atualização |
| `rejected_by` | bigint unsigned | YES | | Quem rejeitou |
| `approved_by` | bigint unsigned | YES | | Quem aprovou |

### 🚨 **SELLER_PROFILES - CAMPOS CORRETOS:**
- ✅ `company_name` (não `business_name`)
- ✅ `commission_rate` (não `commission_percentage`)
- ✅ Status: `pending|pending_approval|approved|rejected|suspended`

---

## 📂 **TABELA: categories**

| Campo | Tipo | Null | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | bigint unsigned | NO | | Chave primária |
| `name` | varchar(255) | NO | | Nome categoria |
| `slug` | varchar(255) | NO | | Slug único |
| `icon` | varchar(50) | YES | | Ícone |
| `description` | text | YES | | Descrição |
| **`image_path`** | varchar(255) | YES | | **Caminho imagem** ⚠️ |
| `parent_id` | bigint unsigned | YES | | Categoria pai |
| `is_active` | tinyint(1) | NO | 1 | Ativo |
| `sort_order` | int | NO | 0 | Ordem exibição |
| `created_at` | timestamp | YES | | Data criação |
| `updated_at` | timestamp | YES | | Data atualização |

---

## 📦 **TABELA: products**

| Campo | Tipo | Null | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | bigint unsigned | NO | | Chave primária |
| `seller_id` | bigint unsigned | NO | | FK seller_profiles |
| `category_id` | bigint unsigned | NO | | FK categories |
| `name` | varchar(255) | NO | | Nome produto |
| `slug` | varchar(255) | NO | | Slug único |
| `description` | text | NO | | Descrição completa |
| `short_description` | text | YES | | Descrição curta |
| `price` | decimal(10,2) | NO | | Preço |
| `compare_at_price` | decimal(10,2) | YES | | Preço "de" |
| `cost` | decimal(10,2) | YES | | Custo |
| `stock_quantity` | int | NO | 0 | Quantidade estoque |
| `stock_status` | enum | NO | in_stock | Status estoque |
| `sku` | varchar(255) | YES | | Código SKU |
| `barcode` | varchar(100) | YES | | Código barras |
| `weight` | decimal(8,2) | YES | | Peso |
| `length` | decimal(8,2) | YES | | Comprimento |
| `width` | decimal(8,2) | YES | | Largura |
| `height` | decimal(8,2) | YES | | Altura |
| `status` | enum | NO | draft | Status: draft/active/inactive |
| `featured` | tinyint(1) | NO | 0 | Produto destaque |
| `digital` | tinyint(1) | NO | 0 | Produto digital |
| `downloadable_files` | json | YES | | Arquivos download |
| `meta_title` | varchar(255) | YES | | SEO título |
| `meta_description` | text | YES | | SEO descrição |
| `meta_keywords` | text | YES | | SEO palavras |
| `views_count` | int | NO | 0 | Visualizações |
| `sales_count` | int | NO | 0 | Vendas |
| `rating_average` | decimal(3,2) | YES | | Nota média |
| `rating_count` | int | NO | 0 | Qtd avaliações |
| `published_at` | timestamp | YES | | Data publicação |
| `brand` | varchar(100) | YES | | Marca |
| `model` | varchar(100) | YES | | Modelo |
| `warranty_months` | int | YES | | Meses garantia |
| `tags` | json | YES | | Tags |
| `attributes` | json | YES | | Atributos |
| `dimensions` | json | YES | | Dimensões |
| `shipping_class` | varchar(50) | YES | | Classe frete |
| `deleted_at` | timestamp | YES | | Soft delete |
| `created_at` | timestamp | YES | | Data criação |
| `updated_at` | timestamp | YES | | Data atualização |

### 🚨 **PRODUCTS - CAMPOS IMPORTANTES:**
- ✅ `stock_status`: `in_stock|out_of_stock|backorder`
- ✅ `status`: `draft|active|inactive`

---

## 🖼️ **TABELA: product_images**

| Campo | Tipo | Null | Default | Descrição |
|-------|------|------|---------|-----------|
| `id` | bigint unsigned | NO | | Chave primária |
| `product_id` | bigint unsigned | NO | | FK products |
| `original_name` | varchar(255) | NO | | Nome original |
| `file_name` | varchar(255) | NO | | Nome arquivo |
| `file_path` | varchar(255) | NO | | Caminho arquivo |
| `mime_type` | varchar(255) | NO | | Tipo MIME |
| `file_size` | int | NO | | Tamanho bytes |
| `width` | int | YES | | Largura px |
| `height` | int | YES | | Altura px |
| `alt_text` | varchar(255) | YES | | Texto alternativo |
| `is_primary` | tinyint(1) | NO | 0 | Imagem principal |
| `sort_order` | int | NO | 0 | Ordem |
| `created_at` | timestamp | YES | | Data criação |
| `updated_at` | timestamp | YES | | Data atualização |

---

## 📋 **REFERÊNCIA RÁPIDA - SEEDERS**

### ✅ **SellerProfile::create([**
```php
'user_id' => $userId,
'company_name' => 'Nome Empresa',        // ✅ company_name
'document_type' => 'cnpj',
'document_number' => '12345678000123',
'phone' => '11999999999',
'status' => 'approved',
'commission_rate' => 10.00,              // ✅ commission_rate
```

### ✅ **Category::create([**
```php
'name' => 'Eletrônicos',
'slug' => 'eletronicos',
'description' => 'Descrição...',
'image_path' => 'categories/image.jpg',  // ✅ image_path
'parent_id' => null,
'is_active' => true,
'sort_order' => 1,
```

### ✅ **Product::create([**
```php
'seller_id' => $sellerId,
'category_id' => $categoryId,
'name' => 'Produto Nome',
'slug' => 'produto-nome',
'description' => 'Descrição completa',
'short_description' => 'Resumo',
'price' => 99.99,
'stock_quantity' => 10,
'stock_status' => 'in_stock',            // ✅ in_stock
'status' => 'active',                    // ✅ active
'featured' => true,
'brand' => 'Marca',
'model' => 'Modelo',
```

---

## ⚠️ **ERROS COMUNS EVITADOS:**

| ❌ Erro Comum | ✅ Campo Correto |
|---------------|-----------------|
| `business_name` | `company_name` |
| `commission_percentage` | `commission_rate` |
| `image_url` | `image_path` |
| `business_type` | *Campo não existe* |
| `phone` (seller) | *Já existe em users* |

---

**🎯 Use sempre este documento como referência para evitar erros nos seeders e formulários!**