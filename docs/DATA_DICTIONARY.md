# 📚 DICIONÁRIO DE DADOS - MARKETPLACE B2C
*Última atualização: 31/08/2025 04:36:23*

## 🎯 OBJETIVO
Este documento estabelece a nomenclatura padrão e inequívoca para todos os campos do banco de dados, evitando inconsistências entre migrations, models, factories, testes e views.

**⚠️ IMPORTANTE:** Este arquivo foi gerado automaticamente pelo comando `php artisan app:check-consistency --update-dictionary`

---

## 📋 TABELAS E CAMPOS

### 1. CART_ITEMS (Cart items)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **cart_id** | `bigint unsigned` | NO | - | FK para cart |
| **product_id** | `bigint unsigned` | NO | - | FK para product |
| **product_variation_id** | `bigint unsigned` | YES | NULL | FK para product_variation |
| **quantity** | `int` | NO | - | Quantidade |
| **unit_price** | `decimal(10,2)` | NO | - | Preço unitário no momento da adição |
| **total_price** | `decimal(10,2)` | NO | - | Preço total (quantity * unit_price) |
| **product_snapshot** | `json` | YES | NULL | Snapshot dos dados do produto |
| **variation_snapshot** | `json` | YES | NULL | Snapshot da variação |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 2. CARTS (Carts)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **user_id** | `bigint unsigned` | YES | NULL | FK para user |
| **session_id** | `varchar(255)` | YES | NULL | Para usuários não logados |
| **total_amount** | `decimal(10,2)` | NO | 0.00 | Campo total_amount |
| **total_items** | `int` | NO | 0 | Campo total_items |
| **shipping_data** | `json` | YES | NULL | Dados de entrega por vendedor |
| **coupon_data** | `json` | YES | NULL | Cupons aplicados |
| **last_activity** | `timestamp` | NO | CURRENT_TIMESTAMP | Campo last_activity |
| **expires_at** | `timestamp` | YES | NULL | Timestamp |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 3. CATEGORIES (Categories)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **name** | `varchar(255)` | NO | - | Nome |
| **slug** | `varchar(255)` | NO | - | URL amigável |
| **icon** | `varchar(50)` | YES | NULL | Campo icon |
| **description** | `text` | YES | NULL | Descrição |
| **image_path** | `varchar(255)` | YES | NULL | Campo image_path |
| **parent_id** | `bigint unsigned` | YES | NULL | FK para parent |
| **is_active** | `tinyint(1)` | NO | 1 | Campo booleano |
| **sort_order** | `int` | NO | 0 | Campo sort_order |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 4. ORDER_ITEMS (Order items)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **order_id** | `bigint unsigned` | NO | - | FK para order |
| **sub_order_id** | `bigint unsigned` | NO | - | FK para sub_order |
| **product_id** | `bigint unsigned` | NO | - | FK para product |
| **product_variation_id** | `bigint unsigned` | YES | NULL | FK para product_variation |
| **product_name** | `varchar(255)` | NO | - | Nome do produto no momento da compra |
| **product_sku** | `varchar(255)` | YES | NULL | SKU do produto no momento da compra |
| **quantity** | `int` | NO | - | Quantidade |
| **unit_price** | `decimal(10,2)` | NO | - | Preço unitário no momento da compra |
| **total_price** | `decimal(10,2)` | NO | - | Preço total (quantity * unit_price) |
| **product_snapshot** | `json` | YES | NULL | Snapshot completo do produto |
| **variation_snapshot** | `json` | YES | NULL | Snapshot da variação escolhida |
| **commission_rate** | `decimal(5,2)` | NO | - | Taxa de comissão aplicada |
| **commission_amount** | `decimal(10,2)` | NO | - | Valor da comissão |
| **seller_amount** | `decimal(10,2)` | NO | - | Valor que o vendedor recebe |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 5. ORDERS (Orders)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **order_number** | `varchar(255)` | NO | - | Campo order_number |
| **user_id** | `bigint unsigned` | NO | - | FK para user |
| **status** | `enum('pending','confirmed','processing','shipped','delivered','cancelled','refunded')` | NO | pending | Status do registro |
| **subtotal** | `decimal(10,2)` | NO | - | Campo subtotal |
| **shipping_total** | `decimal(10,2)` | NO | 0.00 | Campo shipping_total |
| **tax_total** | `decimal(10,2)` | NO | 0.00 | Campo tax_total |
| **discount_total** | `decimal(10,2)` | NO | 0.00 | Campo discount_total |
| **total** | `decimal(10,2)` | NO | - | Campo total |
| **currency** | `varchar(3)` | NO | BRL | Campo currency |
| **payment_status** | `enum('pending','paid','failed','refunded','partially_refunded')` | NO | pending | Campo payment_status |
| **payment_method** | `enum('pix','credit_card','boleto')` | YES | NULL | Campo payment_method |
| **billing_address** | `json` | NO | - | Campo billing_address |
| **shipping_address** | `json` | YES | NULL | Campo shipping_address |
| **customer_notes** | `json` | YES | NULL | Campo customer_notes |
| **admin_notes** | `json` | YES | NULL | Campo admin_notes |
| **coupon_data** | `json` | YES | NULL | Campo coupon_data |
| **mp_payment_id** | `varchar(255)` | YES | NULL | ID do pagamento no Mercado Pago |
| **confirmed_at** | `timestamp` | YES | NULL | Timestamp |
| **shipped_at** | `timestamp` | YES | NULL | Timestamp |
| **delivered_at** | `timestamp` | YES | NULL | Timestamp |
| **deleted_at** | `timestamp` | YES | NULL | Timestamp |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 6. PRODUCT_IMAGES (Product images)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **product_id** | `bigint unsigned` | NO | - | FK para product |
| **original_name** | `varchar(255)` | NO | - | Campo original_name |
| **file_name** | `varchar(255)` | NO | - | Campo file_name |
| **file_path** | `varchar(255)` | NO | - | Campo file_path |
| **mime_type** | `varchar(255)` | NO | - | Campo mime_type |
| **file_size** | `int` | NO | - | Tamanho em bytes |
| **width** | `int` | YES | NULL | Campo width |
| **height** | `int` | YES | NULL | Campo height |
| **alt_text** | `varchar(255)` | YES | NULL | Campo alt_text |
| **is_primary** | `tinyint(1)` | NO | 0 | Campo booleano |
| **sort_order** | `int` | NO | 0 | Campo sort_order |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 7. PRODUCT_VARIATIONS (Product variations)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **product_id** | `bigint unsigned` | NO | - | FK para product |
| **name** | `varchar(255)` | NO | - | Nome da variação: Tamanho, Cor, etc |
| **value** | `varchar(255)` | NO | - | Valor da variação: M, Azul, etc |
| **price_adjustment** | `decimal(8,2)` | NO | 0.00 | Ajuste no preço (+/-) |
| **stock_quantity** | `int` | NO | 0 | Campo stock_quantity |
| **sku_suffix** | `varchar(255)` | YES | NULL | Sufixo do SKU para esta variação |
| **weight_adjustment** | `decimal(8,2)` | NO | 0.00 | Ajuste no peso em gramas (+/-) |
| **is_active** | `tinyint(1)` | NO | 1 | Campo booleano |
| **sort_order** | `int` | NO | 0 | Campo sort_order |
| **meta_data** | `json` | YES | NULL | Dados extras em JSON |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 8. PRODUCTS (Products)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **seller_id** | `bigint unsigned` | NO | - | FK para seller |
| **category_id** | `bigint unsigned` | NO | - | FK para category |
| **name** | `varchar(255)` | NO | - | Nome |
| **slug** | `varchar(255)` | NO | - | URL amigável |
| **description** | `text` | NO | - | Descrição |
| **short_description** | `text` | YES | NULL | Campo short_description |
| **price** | `decimal(10,2)` | NO | - | Preço |
| **compare_at_price** | `decimal(10,2)` | YES | NULL | Preço antes do desconto |
| **cost** | `decimal(10,2)` | YES | NULL | Custo do produto |
| **stock_quantity** | `int` | NO | 0 | Campo stock_quantity |
| **stock_status** | `enum('in_stock','out_of_stock','backorder')` | NO | in_stock | Campo stock_status |
| **sku** | `varchar(255)` | YES | NULL | Campo sku |
| **barcode** | `varchar(100)` | YES | NULL | Código de barras |
| **weight** | `decimal(8,2)` | YES | NULL | Peso em gramas |
| **length** | `decimal(8,2)` | YES | NULL | Comprimento em cm |
| **width** | `decimal(8,2)` | YES | NULL | Largura em cm |
| **height** | `decimal(8,2)` | YES | NULL | Altura em cm |
| **status** | `enum('draft','active','inactive')` | NO | draft | Status do registro |
| **featured** | `tinyint(1)` | NO | 0 | Produto em destaque |
| **digital** | `tinyint(1)` | NO | 0 | Produto digital |
| **downloadable_files** | `json` | YES | NULL | Arquivos para download |
| **meta_title** | `varchar(255)` | YES | NULL | Campo meta_title |
| **meta_description** | `text` | YES | NULL | Campo meta_description |
| **meta_keywords** | `text` | YES | NULL | Palavras-chave SEO |
| **views_count** | `int` | NO | 0 | Contador |
| **sales_count** | `int` | NO | 0 | Total de vendas |
| **rating_average** | `decimal(3,2)` | YES | NULL | Campo rating_average |
| **rating_count** | `int` | NO | 0 | Total de avaliações |
| **published_at** | `timestamp` | YES | NULL | Timestamp |
| **brand** | `varchar(100)` | YES | NULL | Campo brand |
| **model** | `varchar(100)` | YES | NULL | Campo model |
| **warranty_months** | `int` | YES | NULL | Campo warranty_months |
| **tags** | `json` | YES | NULL | Campo tags |
| **attributes** | `json` | YES | NULL | Campo attributes |
| **dimensions** | `json` | YES | NULL | Campo dimensions |
| **shipping_class** | `varchar(50)` | YES | NULL | Campo shipping_class |
| **deleted_at** | `timestamp` | YES | NULL | Timestamp |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 9. SELLER_PROFILES (Seller profiles)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **user_id** | `bigint unsigned` | NO | - | FK para user |
| **document_type** | `varchar(10)` | YES | NULL | Tipo de documento (CPF/CNPJ) |
| **document_number** | `varchar(20)` | YES | NULL | Número do documento |
| **company_name** | `varchar(255)` | YES | NULL | Nome da empresa |
| **address_proof_path** | `varchar(255)` | YES | NULL | Campo address_proof_path |
| **identity_proof_path** | `varchar(255)` | YES | NULL | Caminho do documento de identidade |
| **phone** | `varchar(20)` | YES | NULL | Telefone comercial |
| **address** | `varchar(255)` | YES | NULL | Endereço completo |
| **city** | `varchar(100)` | YES | NULL | Cidade |
| **state** | `varchar(2)` | YES | NULL | Estado (UF) |
| **postal_code** | `varchar(10)` | YES | NULL | CEP formato: 00000-000 |
| **bank_name** | `varchar(100)` | YES | NULL | Nome do banco |
| **bank_agency** | `varchar(20)` | YES | NULL | Campo bank_agency |
| **bank_account** | `varchar(50)` | YES | NULL | Conta bancária |
| **status** | `enum('pending','pending_approval','approved','rejected','suspended')` | NO | pending | Status do registro |
| **rejection_reason** | `text` | YES | NULL | Campo rejection_reason |
| **commission_rate** | `decimal(5,2)` | NO | 10.00 | Taxa de comissão do vendedor |
| **product_limit** | `int` | NO | 100 | Campo product_limit |
| **mp_access_token** | `varchar(500)` | YES | NULL | Token do Mercado Pago |
| **mp_user_id** | `varchar(100)` | YES | NULL | FK para mp_user |
| **mp_connected** | `tinyint(1)` | NO | 0 | Campo mp_connected |
| **approved_at** | `timestamp` | YES | NULL | Timestamp |
| **rejected_at** | `timestamp` | YES | NULL | Timestamp |
| **submitted_at** | `timestamp` | YES | NULL | Data de submissão dos documentos |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |
| **rejected_by** | `bigint unsigned` | YES | NULL | Campo rejected_by |
| **approved_by** | `bigint unsigned` | YES | NULL | Campo approved_by |

### 10. SELLER_SHIPPING_OPTIONS (Seller shipping options)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 11. SUB_ORDERS (Sub orders)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **sub_order_number** | `varchar(255)` | NO | - | Campo sub_order_number |
| **order_id** | `bigint unsigned` | NO | - | FK para order |
| **seller_id** | `bigint unsigned` | NO | - | FK para seller |
| **status** | `enum('pending','confirmed','processing','shipped','delivered','cancelled')` | NO | pending | Status do registro |
| **subtotal** | `decimal(10,2)` | NO | - | Campo subtotal |
| **shipping_cost** | `decimal(10,2)` | NO | 0.00 | Campo shipping_cost |
| **seller_amount** | `decimal(10,2)` | NO | - | Valor que o vendedor recebe |
| **commission_amount** | `decimal(10,2)` | NO | - | Comissão do marketplace |
| **commission_rate** | `decimal(5,2)` | NO | - | Taxa de comissão aplicada |
| **shipping_method** | `json` | YES | NULL | Método de entrega escolhido |
| **tracking_info** | `json` | YES | NULL | Informações de rastreamento |
| **seller_notes** | `text` | YES | NULL | Campo seller_notes |
| **shipped_at** | `timestamp` | YES | NULL | Timestamp |
| **delivered_at** | `timestamp` | YES | NULL | Timestamp |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 12. TRANSACTIONS (Transactions)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **order_id** | `bigint unsigned` | NO | - | FK para order |
| **seller_id** | `bigint unsigned` | NO | - | FK para seller |
| **mp_payment_id** | `varchar(255)` | YES | NULL | ID do pagamento no Mercado Pago |
| **type** | `enum('payment','refund','commission','split')` | NO | payment | Campo type |
| **status** | `enum('pending','approved','rejected','cancelled')` | NO | pending | Status do registro |
| **amount** | `decimal(10,2)` | NO | - | Campo amount |
| **commission_rate** | `decimal(5,2)` | NO | - | Taxa de comissão aplicada |
| **commission_amount** | `decimal(10,2)` | NO | - | Valor da comissão |
| **seller_amount** | `decimal(10,2)` | NO | - | Valor líquido para o vendedor |
| **mp_collector_id** | `varchar(255)` | YES | NULL | ID do recebedor no MP |
| **mp_response** | `json` | YES | NULL | Resposta completa do MP |
| **notes** | `text` | YES | NULL | Campo notes |
| **processed_at** | `timestamp` | YES | NULL | Timestamp |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |

### 13. USERS (Users)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------||
| **id** | `bigint unsigned` | NO | - | ID único do registro |
| **name** | `varchar(255)` | NO | - | Nome |
| **email** | `varchar(255)` | NO | - | Endereço de email |
| **email_verified_at** | `timestamp` | YES | NULL | Timestamp |
| **password** | `varchar(255)` | NO | - | Senha criptografada |
| **role** | `enum('admin','seller','customer')` | NO | customer | Campo role |
| **phone** | `varchar(255)` | YES | NULL | Número de telefone |
| **is_active** | `tinyint(1)` | NO | 1 | Campo booleano |
| **remember_token** | `varchar(100)` | YES | NULL | Campo remember_token |
| **created_at** | `timestamp` | YES | NULL | Timestamp |
| **updated_at** | `timestamp` | YES | NULL | Timestamp |
| **deleted_at** | `timestamp` | YES | NULL | Timestamp |

---

## 🔑 CONVENÇÕES DE NOMENCLATURA

### Regras Gerais:
1. **snake_case** para todos os nomes de campos
2. **Singular** para nomes de tabelas que representam uma entidade
3. **Plural** apenas para tabelas de relacionamento muitos-para-muitos
4. **_id** sufixo para chaves estrangeiras
5. **is_** prefixo para campos booleanos
6. **_at** sufixo para timestamps
7. **_count** sufixo para contadores

### Campos Padronizados:
- **company_name** - SEMPRE usar este nome (NUNCA business_name)
- **phone** - Telefone (não telephone, tel, etc)
- **address** - Endereço (não street, location, etc)
- **postal_code** - CEP (não zip_code, cep, etc)
- **document_type** - Tipo de documento (CPF/CNPJ)
- **document_number** - Número do documento

---

## ⚠️ IMPORTANTE

**Este dicionário é a fonte única da verdade para nomenclatura de campos.**

Qualquer alteração deve ser:
1. Documentada primeiro aqui
2. Aplicada em migrations
3. Atualizada em models
4. Corrigida em factories
5. Ajustada em seeders
6. Alterada em testes
7. Modificada em views/forms

---

## 📝 HISTÓRICO DE MUDANÇAS

| Data | Mudança | Responsável |
|------|---------|-------------|
| 31/08/2025 04:36:23 | Atualização automática via app:check-consistency | Sistema |

