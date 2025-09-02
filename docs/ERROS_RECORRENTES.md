# ERROS RECORRENTES - Marketplace B2C

Este documento lista erros recorrentes encontrados no sistema e suas respectivas soluções.

## 1. Erro de Truncamento de Dados - Campo `status` em `seller_profiles`

### Descrição do Erro
```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status' at row 1
```

### Causa Raiz
Inconsistência entre diferentes camadas do sistema quanto aos valores permitidos no campo `status`:

#### Banco de Dados (Migration mais recente)
- **Arquivo**: `database/migrations/2025_08_31_201440_align_seller_profiles_with_dictionary.php`
- **Valores permitidos**: `'pending', 'approved', 'rejected', 'suspended'`
- **Valor removido**: `'pending_approval'` foi convertido para `'pending'`

#### Factory
- **Arquivo**: `database/factories/SellerProfileFactory.php`
- **Problema**: Ainda utiliza `'pending_approval'` na linha 39
- **Método `pending()`**: Linha 68 ainda retorna `'pending_approval'`

#### Model
- **Arquivo**: `app/Models/SellerProfile.php`
- **Verificar**: Se há constantes ou validações usando `'pending_approval'`

### Locais Afetados

#### Arquivos com `pending_approval` (6 encontrados):
1. `database/factories/SellerProfileFactory.php` - Linhas 39 e 68
2. `database/seeders/MassDataSeeder.php`
3. `database/seeders/UserSeeder.php`
4. `database/migrations/2025_08_28_105748_complete_seller_profiles_fields_according_to_data_dictionary.php`
5. `database/migrations/2025_08_28_041339_fix_seller_profiles_table_defaults.php`
6. `database/migrations/2025_08_31_201440_align_seller_profiles_with_dictionary.php` (migration que remove o valor)

#### Testes que falham (10 no total):
1. `Tests\Feature\UserManagementTest::seller_profile_relationship_works`
2. `Tests\Feature\UserManagementTest::can_check_user_roles`
3. `Tests\Feature\RoleSystemTest` - múltiplos métodos
4. `Tests\Feature\SellerJourneyTest` - múltiplos métodos
5. Outros testes que criam SellerProfile via factory

#### Funcionalidades potencialmente afetadas:
- Registro de novos vendedores
- Aprovação de vendedores
- Listagem de vendedores pendentes
- Dashboard do vendedor

### Solução Necessária

#### 1. Atualizar Factory
```php
// database/factories/SellerProfileFactory.php
// Linha 39 - Remover 'pending_approval'
'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'suspended']),

// Linha 68 - Método pending()
'status' => 'pending',
```

#### 2. Verificar e atualizar Model
Buscar por referências a `pending_approval` em:
- Constantes do modelo
- Métodos de validação
- Scopes
- Accessors/Mutators

#### 3. Atualizar Seeds
Verificar se algum seeder usa `'pending_approval'`

#### 4. Atualizar Frontend/Views
Buscar em arquivos Blade e JavaScript por:
- `pending_approval`
- Lógica que dependa deste status

### Comandos para Verificação
```bash
# Buscar todas as ocorrências de 'pending_approval' no código
grep -r "pending_approval" --include="*.php" --include="*.js" --include="*.blade.php"

# Verificar estrutura atual no banco
php artisan tinker
>>> \DB::select("SHOW COLUMNS FROM seller_profiles WHERE Field = 'status'");
```

### Prevenção Futura
1. Manter dicionário de dados atualizado
2. Usar constantes no Model para valores de enum
3. Criar testes específicos para validar valores de enum
4. Documentar mudanças em campos enum no changelog

---

## 2. Erro de Campo Inexistente - `business_name` em `seller_profiles`

### Descrição do Erro
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'business_name' in 'field list'
```

### Causa Raiz
Tentativa de inserir dados no campo `business_name` que não existe na estrutura atual da tabela `seller_profiles`.

#### Estrutura Real da Tabela
- **Arquivo**: `app/Models/SellerProfile.php`
- **Campos disponíveis no fillable**:
  - `user_id`
  - `document_type`
  - `document_number`
  - `company_name` ✅ (campo correto)
  - `address_proof_path`
  - `identity_proof_path`
  - `phone`
  - `address`
  - `city`
  - `state`
  - `postal_code`
  - `bank_name`
  - `bank_agency`
  - `bank_account`
  - `status`
  - `rejection_reason`
  - `commission_rate`
  - `product_limit`
  - `mp_access_token`
  - `mp_user_id`
  - `mp_connected`
  - `approved_at`
  - `approved_by`
  - `rejected_at`
  - `rejected_by`
  - `submitted_at`

### Locais Afetados

#### Seeders que podem ter o erro:
- `database/seeders/HighVolumeSeeder.php` - Linha ~120 (usa `business_name`)
- Outros seeders que tentam usar campo `business_name`

### Solução Necessária

#### 1. Corrigir Seeders
```php
// ❌ ERRO - Campo inexistente
'business_name' => $businessName . ' Ltda',

// ✅ CORRETO - Usar company_name
'company_name' => $businessName,
```

#### 2. Verificar Migrations
Se necessário criar o campo `business_name`:
```php
// Migration para adicionar business_name (se realmente necessário)
Schema::table('seller_profiles', function (Blueprint $table) {
    $table->string('business_name')->nullable()->after('company_name');
});
```

#### 3. Atualizar Model (se adicionar o campo)
```php
// app/Models/SellerProfile.php
protected $fillable = [
    // ... outros campos ...
    'company_name',
    'business_name', // adicionar se criar o campo
    // ... outros campos ...
];
```

### Diferença entre campos
- **`company_name`**: Nome da empresa (existente)
- **`business_name`**: Nome fantasia da empresa (não existe)

### Comandos para Verificação
```bash
# Verificar estrutura atual da tabela
php artisan tinker
>>> \Schema::getColumnListing('seller_profiles');

# Buscar uso incorreto de business_name
grep -r "business_name" --include="*.php" database/seeders/
```

### Prevenção Futura
1. **Sempre consultar o Model** antes de criar seeders
2. **Verificar fillable** antes de inserir dados
3. **Usar migrations** para adicionar novos campos
4. **Testar seeders** em ambiente isolado primeiro

---

## 3. Configuração de URL para Desenvolvimento - APP_URL obrigatório

### Descrição do Problema
O uso de `http://localhost:8000/` no desenvolvimento pode causar problemas de assets, rotas e funcionalidades específicas do Laravel.

### Configuração Obrigatória
Para desenvolvimento e testes, deve ser **MANDATÓRIO** o uso de:

```env
APP_URL=https://marketplace-b2c.test
```

### Problemas com localhost:8000
- Assets podem não carregar corretamente
- URLs absolutas podem ser incorretas
- Funcionalidades de autenticação podem falhar
- Rotas nomeadas podem gerar URLs inválidas
- Problemas com CSRF tokens
- Configurações de cookies podem não funcionar

### Configuração Correta

#### 1. Arquivo .env
```env
# ❌ INCORRETO
APP_URL=http://localhost:8000

# ✅ CORRETO
APP_URL=https://marketplace-b2c.test
```

#### 2. Configuração do Laragon/Valet
```bash
# Para Laragon
# 1. Criar pasta do projeto em C:/laragon/www/marketplace-b2c
# 2. Acessar via: https://marketplace-b2c.test

# Para Laravel Valet
valet link marketplace-b2c
valet secure marketplace-b2c
```

#### 3. Hosts (se necessário)
```
# Windows: C:\Windows\System32\drivers\etc\hosts
# Linux/Mac: /etc/hosts
127.0.0.1 marketplace-b2c.test
```

### Verificação da Configuração
```bash
# Verificar se PHP está no PATH (deve retornar versão)
php -v
where php

# Verificar configuração atual
php artisan config:show app.url

# Limpar cache após mudança
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Benefícios da Configuração Correta
- ✅ URLs absolutas funcionam corretamente
- ✅ Assets carregam sem problemas
- ✅ Ambiente mais próximo da produção
- ✅ HTTPS habilitado (segurança)
- ✅ Cookies e sessões funcionam adequadamente
- ✅ Funcionalidades de terceiros (APIs) funcionam

### URLs de Teste Padronizadas
```
Homepage: https://marketplace-b2c.test
Produto: https://marketplace-b2c.test/produto/2
Busca: https://marketplace-b2c.test/buscar?q=gamer
Admin: https://marketplace-b2c.test/admin
Seller: https://marketplace-b2c.test/seller
```

### Prevenção Futura
1. **Documentar sempre** a URL correta nos READMEs
2. **Verificar APP_URL** antes de testar funcionalidades
3. **Usar ambiente padronizado** em toda a equipe
4. **Incluir verificação** nos testes automatizados

---

## 4. Verbosidade Desnecessária em Comandos PHP - PATH vs Caminho Completo

### Descrição do Problema
Uso desnecessário de caminhos completos para PHP quando ele já está configurado no PATH do sistema.

### Situação Atual Confirmada
```bash
$ php -v
PHP 8.3.18 (cli) (built: Mar 11 2025 22:44:57) (NTS Visual C++ 2019 x64)

$ where php
C:\laragon\bin\php\php-8.3.18-nts-Win32-vs16-x64\php.exe
```

### Comandos Corretos vs Incorretos

#### ❌ VERBOSE - Desnecessário
```bash
C:/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan serve
C:/laragon/bin/php/php-8.3.18-nts-Win32-vs16-x64/php.exe artisan test
```

#### ✅ CORRETO - Simples e direto
```bash
php artisan serve
php artisan test
php artisan config:clear
php artisan route:clear
```

### Quando Usar Caminho Completo
Apenas em situações específicas:
- **Múltiplas versões** do PHP instaladas
- **Scripts automatizados** que precisam de versão específica
- **CI/CD** onde PATH pode não estar configurado
- **Debugging** de problemas de versão

### Verificação Padrão
Sempre verificar se PHP está no PATH antes de usar caminhos completos:
```bash
# Windows
php -v
where php

# Linux/Mac
php -v
which php
```

### Configuração do PATH (se necessário)
```bash
# Windows - Adicionar ao PATH do usuário
setx PATH "%PATH%;C:\laragon\bin\php\php-8.3.18-nts-Win32-vs16-x64"

# Ou via interface gráfica:
# Sistema > Variáveis de ambiente > PATH > Novo
```

### Benefícios do PATH Configurado
- ✅ **Comandos mais limpos** e legíveis
- ✅ **Portabilidade** entre ambientes
- ✅ **Velocidade** de digitação
- ✅ **Compatibilidade** com scripts
- ✅ **Facilidade** de manutenção

### Prevenção Futura
1. **Verificar PATH** antes de documentar comandos
2. **Usar forma simples** quando PHP estiver no PATH
3. **Documentar pré-requisitos** de configuração
4. **Padronizar comandos** na documentação

---

## 5. Relacionamento Incorreto - Product::seller() apontando para User ao invés de SellerProfile

### Descrição do Problema
O relacionamento `seller()` no modelo `Product` estava apontando incorretamente para `User::class`, causando erro no template ao tentar acessar `$product->seller->user->name`.

### Erro no Template
```blade
<x-icon name="user" size="5" />
<span class="font-roboto">por {{ $product->seller->user->name }}</span>
```

### Causa Raiz
**Estrutura de dados:**
- `products.seller_id` → `users.id`
- `seller_profiles.user_id` → `users.id`
- Relacionamento incorreto no modelo `Product`

### Configuração Incorreta (Product.php)
```php
// ❌ INCORRETO - Relacionamento direto com User
public function seller(): BelongsTo
{
    return $this->belongsTo(User::class, 'seller_id');
}

public function sellerProfile(): BelongsTo
{
    return $this->belongsTo(SellerProfile::class, 'seller_id', 'user_id');
}
```

### Configuração Correta (Product.php)
```php
// ✅ CORRETO - Relacionamento com SellerProfile
public function seller(): BelongsTo
{
    return $this->belongsTo(SellerProfile::class, 'seller_id', 'user_id');
}

public function sellerUser(): BelongsTo
{
    return $this->belongsTo(User::class, 'seller_id');
}
```

### Controller Atualizado (HomeController.php)
```php
// ❌ INCORRETO
->with(['seller', 'category', 'images'])

// ✅ CORRETO - Carregar relacionamento aninhado
->with(['seller.user', 'category', 'images'])
```

### Template com Solução Defensiva
```blade
<div class="flex items-center space-x-2 text-cinza-pedra">
    <x-icon name="user" size="5" />
    <span class="font-roboto">por {{ $product->seller->user->name ?? $product->sellerUser->name ?? 'Vendedor' }}</span>
</div>
```

### Controller com Fallback
```php
// Carregar ambos os relacionamentos para garantir compatibilidade
$product = Product::where('status', 'active')
    ->with(['seller.user', 'sellerUser', 'category', 'images'])
    ->findOrFail($id);
```

### Locais Afetados
1. **Modelo**: `app/Models/Product.php` - Relacionamento `seller()`
2. **Controller**: `app/Http/Controllers/HomeController.php` - Método `product()`
3. **Template**: `resources/views/product.blade.php` - Exibição do vendedor
4. **Produtos Relacionados**: Mesmo problema nos produtos relacionados

### Verificação de Integridade
```bash
# Verificar estrutura da tabela products
php artisan tinker
>>> \Schema::getColumnListing('products');
>>> \Schema::getColumnListing('seller_profiles');

# Testar relacionamento
>>> $product = App\Models\Product::with('seller.user')->first();
>>> $product->seller->user->name;
```

### Prevenção Futura
1. **Documentar relacionamentos** entre modelos
2. **Verificar eager loading** nos controllers
3. **Testar relacionamentos** em ambiente de desenvolvimento
4. **Padronizar nomenclatura** de relacionamentos

---

## 6. [Próximo erro será documentado aqui]

---

## Como Usar Este Documento

1. **Ao encontrar um erro recorrente**: Adicione-o seguindo o formato acima
2. **Antes de fazer alterações**: Consulte este documento para evitar reintroduzir erros
3. **Durante code review**: Verifique se as mudanças não conflitam com soluções documentadas
4. **Ao atualizar migrations**: Certifique-se de atualizar todas as camadas afetadas

## Histórico de Atualizações

| Data | Erro Adicionado | Autor |
|------|-----------------|-------|
| 2025-09-01 | Erro de Truncamento - status em seller_profiles | Sistema |
| 2025-09-01 | Campo Inexistente - business_name em seller_profiles | Sistema |
| 2025-09-02 | Configuração de URL - APP_URL obrigatório para desenvolvimento | Sistema |
| 2025-09-02 | Verbosidade Desnecessária - PATH vs Caminho Completo | Sistema |
| 2025-09-02 | Relacionamento Incorreto - Product::seller() vs SellerProfile | Sistema |