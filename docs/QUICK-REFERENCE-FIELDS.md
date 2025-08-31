# 🚀 REFERÊNCIA RÁPIDA - CAMPOS DAS TABELAS

## 👥 **USERS**
```php
User::create([
    'name' => 'Nome Usuário',
    'email' => 'user@email.com',
    'password' => Hash::make('password'),
    'role' => 'customer', // admin|seller|customer
    'phone' => '11999999999',
    'is_active' => true
]);
```

## 🏪 **SELLER_PROFILES** 
```php
SellerProfile::create([
    'user_id' => $userId,
    'company_name' => 'Empresa LTDA',    // ⚠️ company_name
    'document_type' => 'cnpj',           // cpf|cnpj
    'document_number' => '12345678000123',
    'phone' => '11988887777',
    'address' => 'Rua X, 123',
    'city' => 'São Paulo',
    'state' => 'SP',
    'postal_code' => '01234567',
    'status' => 'approved',              // pending|approved|rejected
    'commission_rate' => 10.00           // ⚠️ commission_rate
]);
```

## 📂 **CATEGORIES**
```php
Category::create([
    'name' => 'Eletrônicos',
    'slug' => 'eletronicos',
    'description' => 'Produtos eletrônicos',
    'image_path' => 'categories/image.jpg', // ⚠️ image_path
    'parent_id' => null,                    // ou ID categoria pai
    'is_active' => true,
    'sort_order' => 1
]);
```

## 📦 **PRODUCTS**
```php
Product::create([
    'seller_id' => $sellerId,
    'category_id' => $categoryId,
    'name' => 'Nome Produto',
    'slug' => 'nome-produto',
    'description' => 'Descrição completa',
    'short_description' => 'Resumo curto',
    'price' => 199.99,
    'stock_quantity' => 50,
    'stock_status' => 'in_stock',        // in_stock|out_of_stock|backorder
    'status' => 'active',                // draft|active|inactive
    'featured' => false,
    'brand' => 'Marca',
    'model' => 'Modelo ABC'
]);
```

## 🖼️ **PRODUCT_IMAGES**
```php
ProductImage::create([
    'product_id' => $productId,
    'original_name' => 'foto.jpg',
    'file_name' => 'produto_123.jpg',
    'file_path' => 'products/produto_123.jpg',
    'mime_type' => 'image/jpeg',
    'file_size' => 1024000,
    'alt_text' => 'Foto do produto',
    'is_primary' => true,
    'sort_order' => 1
]);
```

---

## ⚠️ **CAMPOS QUE SEMPRE CAUSAM ERRO:**

| Tabela | ❌ Nome Errado | ✅ Nome Correto |
|--------|---------------|-----------------|
| seller_profiles | `business_name` | `company_name` |
| seller_profiles | `commission_percentage` | `commission_rate` |
| categories | `image_url` | `image_path` |
| products | `description_short` | `short_description` |

---

## 🔍 **ENUMS IMPORTANTES:**

### User.role:
- `admin` | `seller` | `customer`

### SellerProfile.status:
- `pending` | `pending_approval` | `approved` | `rejected` | `suspended`

### Product.status:
- `draft` | `active` | `inactive`

### Product.stock_status:
- `in_stock` | `out_of_stock` | `backorder`

---

**💡 Mantenha este arquivo aberto durante desenvolvimento!**