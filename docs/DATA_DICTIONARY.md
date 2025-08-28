# 📚 DICIONÁRIO DE DADOS - MARKETPLACE B2C
*Última atualização: 28/08/2025*

## 🎯 OBJETIVO
Este documento estabelece a nomenclatura padrão e inequívoca para todos os campos do banco de dados, evitando inconsistências entre migrations, models, factories, testes e views.

---

## 📋 TABELAS E CAMPOS

### 1. USERS (Usuários)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do usuário |
| name | string(255) | NO | - | Nome completo do usuário |
| email | string(255) | NO | - | Email único |
| email_verified_at | timestamp | YES | NULL | Data/hora de verificação do email |
| password | string(255) | NO | - | Senha criptografada |
| role | enum | NO | 'customer' | Tipo: 'customer', 'seller', 'admin' |
| phone | string(20) | YES | NULL | Telefone com DDD |
| is_active | boolean | NO | true | Se o usuário está ativo |
| is_admin | boolean | NO | false | Se é administrador |
| remember_token | string(100) | YES | NULL | Token de sessão |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 2. SELLER_PROFILES (Perfis de Vendedores)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do perfil |
| user_id | bigInteger | NO | - | FK para users.id |
| **company_name** | string(255) | YES | NULL | Nome da empresa/vendedor |
| document_type | string(10) | YES | NULL | Tipo: 'CPF' ou 'CNPJ' |
| document_number | string(20) | YES | NULL | Número do documento |
| address_proof_path | string(255) | YES | NULL | Caminho do comprovante de endereço |
| identity_proof_path | string(255) | YES | NULL | Caminho do documento de identidade |
| phone | string(20) | YES | NULL | Telefone comercial |
| address | string(255) | YES | NULL | Endereço completo |
| city | string(100) | YES | NULL | Cidade |
| state | string(2) | YES | NULL | Estado (UF) |
| postal_code | string(10) | YES | NULL | CEP formato: 00000-000 |
| bank_name | string(100) | YES | NULL | Nome do banco |
| bank_account | string(50) | YES | NULL | Conta bancária |
| status | enum | NO | 'pending' | Status: 'pending', 'pending_approval', 'approved', 'rejected', 'suspended' |
| rejection_reason | text | YES | NULL | Motivo da rejeição |
| commission_rate | decimal(5,2) | NO | 10.00 | Taxa de comissão (%) |
| product_limit | integer | NO | 100 | Limite de produtos |
| mp_access_token | string(500) | YES | NULL | Token Mercado Pago |
| mp_user_id | string(100) | YES | NULL | ID usuário Mercado Pago |
| mp_connected | boolean | NO | false | Se está conectado ao MP |
| approved_at | timestamp | YES | NULL | Data de aprovação |
| submitted_at | timestamp | YES | NULL | Data de submissão |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 3. CATEGORIES (Categorias)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único da categoria |
| name | string(100) | NO | - | Nome da categoria |
| slug | string(100) | NO | - | URL amigável única |
| description | text | YES | NULL | Descrição da categoria |
| icon | string(50) | YES | NULL | Ícone da categoria |
| parent_id | bigInteger | YES | NULL | FK para categoria pai |
| is_active | boolean | NO | true | Se está ativa |
| sort_order | integer | NO | 0 | Ordem de exibição |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 4. PRODUCTS (Produtos)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do produto |
| seller_id | bigInteger | NO | - | FK para seller_profiles.id |
| category_id | bigInteger | NO | - | FK para categories.id |
| name | string(255) | NO | - | Nome do produto |
| slug | string(255) | NO | - | URL amigável única |
| description | text | YES | NULL | Descrição detalhada |
| short_description | string(500) | YES | NULL | Descrição curta |
| price | decimal(10,2) | NO | - | Preço base |
| compare_at_price | decimal(10,2) | YES | NULL | Preço comparativo |
| cost | decimal(10,2) | YES | NULL | Custo do produto |
| sku | string(100) | YES | NULL | Código SKU único |
| barcode | string(100) | YES | NULL | Código de barras |
| stock_quantity | integer | NO | 0 | Quantidade em estoque |
| stock_status | enum | NO | 'in_stock' | Status: 'in_stock', 'out_of_stock', 'backorder' |
| weight | decimal(8,3) | YES | NULL | Peso em kg |
| length | decimal(8,2) | YES | NULL | Comprimento em cm |
| width | decimal(8,2) | YES | NULL | Largura em cm |
| height | decimal(8,2) | YES | NULL | Altura em cm |
| status | enum | NO | 'draft' | Status: 'draft', 'active', 'inactive', 'archived' |
| featured | boolean | NO | false | Se é destaque |
| digital | boolean | NO | false | Se é produto digital |
| downloadable_files | json | YES | NULL | Arquivos para download |
| meta_title | string(255) | YES | NULL | Título SEO |
| meta_description | text | YES | NULL | Descrição SEO |
| meta_keywords | text | YES | NULL | Palavras-chave SEO |
| views_count | integer | NO | 0 | Contador de visualizações |
| sales_count | integer | NO | 0 | Contador de vendas |
| rating_average | decimal(3,2) | NO | 0.00 | Média de avaliações |
| rating_count | integer | NO | 0 | Total de avaliações |
| published_at | timestamp | YES | NULL | Data de publicação |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 5. PRODUCT_IMAGES (Imagens de Produtos)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único da imagem |
| product_id | bigInteger | NO | - | FK para products.id |
| image_path | string(255) | NO | - | Caminho da imagem |
| alt_text | string(255) | YES | NULL | Texto alternativo |
| is_primary | boolean | NO | false | Se é imagem principal |
| sort_order | integer | NO | 0 | Ordem de exibição |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 6. PRODUCT_VARIATIONS (Variações de Produtos)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único da variação |
| product_id | bigInteger | NO | - | FK para products.id |
| name | string(100) | NO | - | Nome da variação (ex: "Tamanho P") |
| sku | string(100) | YES | NULL | SKU da variação |
| price | decimal(10,2) | YES | NULL | Preço da variação |
| stock_quantity | integer | NO | 0 | Estoque da variação |
| attributes | json | YES | NULL | Atributos (cor, tamanho, etc) |
| is_active | boolean | NO | true | Se está ativa |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 7. CARTS (Carrinhos)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do carrinho |
| user_id | bigInteger | YES | NULL | FK para users.id (null = visitante) |
| session_id | string(100) | YES | NULL | ID da sessão para visitantes |
| status | enum | NO | 'active' | Status: 'active', 'abandoned', 'converted' |
| expires_at | timestamp | YES | NULL | Data de expiração |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 8. CART_ITEMS (Itens do Carrinho)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do item |
| cart_id | bigInteger | NO | - | FK para carts.id |
| product_id | bigInteger | NO | - | FK para products.id |
| variation_id | bigInteger | YES | NULL | FK para product_variations.id |
| quantity | integer | NO | 1 | Quantidade |
| price | decimal(10,2) | NO | - | Preço unitário no momento |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 9. ORDERS (Pedidos)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do pedido |
| user_id | bigInteger | NO | - | FK para users.id |
| order_number | string(50) | NO | - | Número único do pedido |
| status | enum | NO | 'pending' | Status do pedido |
| payment_status | enum | NO | 'pending' | Status do pagamento |
| payment_method | string(50) | YES | NULL | Método de pagamento |
| subtotal | decimal(10,2) | NO | 0.00 | Subtotal |
| shipping_cost | decimal(10,2) | NO | 0.00 | Custo de envio |
| tax_amount | decimal(10,2) | NO | 0.00 | Impostos |
| discount_amount | decimal(10,2) | NO | 0.00 | Desconto |
| total | decimal(10,2) | NO | 0.00 | Total |
| currency | string(3) | NO | 'BRL' | Moeda |
| shipping_address | json | YES | NULL | Endereço de entrega |
| billing_address | json | YES | NULL | Endereço de cobrança |
| customer_notes | text | YES | NULL | Observações do cliente |
| admin_notes | text | YES | NULL | Observações do admin |
| shipped_at | timestamp | YES | NULL | Data de envio |
| delivered_at | timestamp | YES | NULL | Data de entrega |
| cancelled_at | timestamp | YES | NULL | Data de cancelamento |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 10. SUB_ORDERS (Sub-pedidos por Vendedor)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do sub-pedido |
| order_id | bigInteger | NO | - | FK para orders.id |
| seller_id | bigInteger | NO | - | FK para seller_profiles.id |
| sub_order_number | string(50) | NO | - | Número único do sub-pedido |
| status | enum | NO | 'pending' | Status do sub-pedido |
| subtotal | decimal(10,2) | NO | 0.00 | Subtotal |
| commission_amount | decimal(10,2) | NO | 0.00 | Valor da comissão |
| seller_amount | decimal(10,2) | NO | 0.00 | Valor do vendedor |
| shipping_cost | decimal(10,2) | NO | 0.00 | Custo de envio |
| tracking_code | string(100) | YES | NULL | Código de rastreamento |
| shipped_at | timestamp | YES | NULL | Data de envio |
| delivered_at | timestamp | YES | NULL | Data de entrega |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 11. ORDER_ITEMS (Itens do Pedido)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único do item |
| order_id | bigInteger | NO | - | FK para orders.id |
| sub_order_id | bigInteger | YES | NULL | FK para sub_orders.id |
| product_id | bigInteger | NO | - | FK para products.id |
| variation_id | bigInteger | YES | NULL | FK para product_variations.id |
| product_name | string(255) | NO | - | Nome do produto no momento |
| product_sku | string(100) | YES | NULL | SKU no momento |
| quantity | integer | NO | 1 | Quantidade |
| price | decimal(10,2) | NO | - | Preço unitário |
| subtotal | decimal(10,2) | NO | - | Subtotal do item |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

### 12. TRANSACTIONS (Transações Financeiras)
| Campo | Tipo | Nullable | Default | Descrição |
|-------|------|----------|---------|-----------|
| id | bigInteger | NO | AUTO_INCREMENT | ID único da transação |
| order_id | bigInteger | YES | NULL | FK para orders.id |
| sub_order_id | bigInteger | YES | NULL | FK para sub_orders.id |
| seller_id | bigInteger | YES | NULL | FK para seller_profiles.id |
| type | enum | NO | - | Tipo: 'payment', 'refund', 'commission', 'withdrawal' |
| status | enum | NO | 'pending' | Status: 'pending', 'processing', 'completed', 'failed', 'cancelled' |
| amount | decimal(10,2) | NO | - | Valor da transação |
| currency | string(3) | NO | 'BRL' | Moeda |
| gateway | string(50) | YES | NULL | Gateway de pagamento |
| gateway_transaction_id | string(255) | YES | NULL | ID da transação no gateway |
| gateway_response | json | YES | NULL | Resposta do gateway |
| reference_number | string(100) | YES | NULL | Número de referência |
| description | text | YES | NULL | Descrição |
| metadata | json | YES | NULL | Metadados adicionais |
| processed_at | timestamp | YES | NULL | Data de processamento |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | Data de criação |
| updated_at | timestamp | NO | CURRENT_TIMESTAMP | Data de atualização |

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
8. **status** para enums de estado (nunca "state" quando se refere a status)
9. **state** apenas para unidade federativa (UF)

### Campos Padronizados:
- **company_name** - SEMPRE usar este nome (NUNCA business_name)
- **phone** - Telefone (não telephone, tel, etc)
- **address** - Endereço (não street, location, etc)
- **postal_code** - CEP (não zip_code, cep, etc)
- **document_type** - Tipo de documento (CPF/CNPJ)
- **document_number** - Número do documento

### Status Padronizados:
- **Users:** active, inactive, suspended, banned
- **Sellers:** pending, pending_approval, approved, rejected, suspended
- **Products:** draft, active, inactive, archived
- **Orders:** pending, processing, shipped, delivered, cancelled, refunded
- **Payments:** pending, processing, completed, failed, cancelled

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
| 28/08/2025 | Criação inicial do dicionário | Sistema |
| 28/08/2025 | Padronização: business_name → company_name | Sistema |
| 28/08/2025 | Adição de campos de endereço em seller_profiles | Sistema |

---

## 🔍 CHECKLIST DE VALIDAÇÃO

- [ ] Todos os campos em migrations correspondem ao dicionário
- [ ] Models têm $fillable com campos corretos
- [ ] Factories usam nomes corretos
- [ ] Testes referenciam campos corretos
- [ ] Views/Forms usam name="" corretos
- [ ] Validações em Controllers usam campos corretos
- [ ] API Resources retornam campos corretos