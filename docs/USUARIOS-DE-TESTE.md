# Usuários de Teste - Marketplace B2C

## 🎯 **Objetivo**

Sistema de usuários hardcoded para facilitar o desenvolvimento e testes, evitando a necessidade de recriar usuários a cada reset do banco de dados.

## 👥 **Usuários Criados**

### 👑 **Administrador**
- **Email**: `admin@marketplace.com`
- **Senha**: `admin123`
- **Role**: `admin`
- **Status**: Ativo e verificado
- **Acesso**: Painel administrativo completo

### 🏪 **Vendedor**
- **Email**: `vendedor@marketplace.com`
- **Senha**: `vendedor123`
- **Role**: `seller`
- **Status**: Aprovado e ativo
- **Empresa**: Silva Comércio e Eletrônicos Ltda
- **Documento**: CNPJ 12.345.678/0001-90
- **Comissão**: 8.5% (especial)
- **Acesso**: Dashboard do vendedor

### 🛒 **Cliente**
- **Email**: `cliente@marketplace.com`
- **Senha**: `cliente123`
- **Role**: `customer`
- **Status**: Ativo e verificado
- **Acesso**: Área do cliente e loja

## 📦 **Dados Extras Criados**

### **Categoria de Teste**
- **Nome**: Eletrônicos
- **Slug**: `eletronicos`
- **Status**: Ativa

### **Produto de Teste**
- **Nome**: Smartphone Samsung Galaxy A54 128GB
- **SKU**: SAMSUNG-A54-128GB-TEST
- **Preço**: R$ 1.299,90 (de R$ 1.599,90)
- **Estoque**: 15 unidades
- **Status**: Ativo e em destaque
- **Vendedor**: Silva Comércio e Eletrônicos

## 🚀 **Como Usar**

### **Criação Automática (Recomendado)**
```bash
# Criar usuários de teste (preserva existentes)
php artisan users:test

# Recriar usuários de teste (remove e recria)
php artisan users:test --fresh
```

### **Execução via Seeder**
```bash
# Executar apenas o seeder de teste
php artisan db:seed --class=TestUsersSeeder

# Executar seeder completo (inclui usuários de teste)
php artisan db:seed
```

### **Migração Fresh com Testes**
```bash
# Reset completo + dados de teste
php artisan migrate:fresh --seed
```

## 🔗 **Links de Acesso**

### **URLs de Login**
- **Geral**: http://marketplace-b2c.test/login
- **Admin**: http://marketplace-b2c.test/admin/dashboard
- **Vendedor**: http://marketplace-b2c.test/seller/dashboard
- **Loja**: http://marketplace-b2c.test/

### **Área de Registro**
- **Vendedor**: http://marketplace-b2c.test/register/seller
- **Cliente**: http://marketplace-b2c.test/register

## 📁 **Arquivos do Sistema**

### **Seeder Principal**
```
database/seeders/TestUsersSeeder.php
```
- Cria os 3 usuários de teste
- Cria perfil de vendedor aprovado
- Cria categoria e produto de exemplo
- Usa `firstOrCreate` para evitar duplicatas

### **Comando Artisan**
```
app/Console/Commands/CreateTestUsers.php
```
- Comando customizado `users:test`
- Opção `--fresh` para recriar
- Remoção segura com `forceDelete`
- Interface amigável com cores e instruções

### **Integração com DatabaseSeeder**
```
database/seeders/DatabaseSeeder.php
```
- Executado automaticamente no `db:seed`
- Prioridade após usuários protegidos
- Sempre executado (não depende de dados existentes)

## ⚙️ **Configurações Especiais**

### **Vendedor Pré-Aprovado**
- Status: `approved` (pula processo de aprovação)
- Perfil completo preenchido
- Dados bancários configurados
- Mercado Pago: Não conectado (`mp_connected: false`)

### **Comissão Diferenciada**
- Taxa especial: 8.5% (padrão é 10%)
- Limite de produtos: 100
- Aprovado em: Data atual
- Submetido: 2 dias atrás

### **Produto Destacado**
- Featured: `true`
- Marca e modelo definidos
- Garantia: 12 meses
- Publicado automaticamente

## 🔄 **Processo de Remoção (--fresh)**

1. **Busca com Soft Delete**: Inclui registros deletados
2. **Remove Relacionamentos**: SellerProfile → User
3. **Force Delete**: Remove permanentemente do banco
4. **Limpa Produtos**: Remove produtos relacionados
5. **Limpa Categorias**: Remove categoria de teste

## 🧪 **Para Testes**

### **Cenários de Teste Cobertos**
- ✅ Login de admin funcional
- ✅ Vendedor com perfil aprovado
- ✅ Cliente para compras
- ✅ Produto disponível na loja
- ✅ Categoria com produtos
- ✅ Dashboard administrativo
- ✅ Dashboard do vendedor
- ✅ Fluxo de compra completo

### **Dados Realistas**
- Documentos válidos (CNPJ formatado)
- Telefones no padrão brasileiro
- Endereços completos
- Produtos com preços comparativos
- Categorias organizadas

## 💡 **Dicas de Desenvolvimento**

### **Uso Diário**
```bash
# Toda manhã, garanta que os usuários existem
php artisan users:test

# Se algo deu errado, recrie tudo
php artisan users:test --fresh
```

### **Para Demos**
- Dados já preenchidos e consistentes
- Vendedor com histórico de aprovação
- Produto atrativo para apresentações

### **Para Testes Automatizados**
- Usuários sempre disponíveis
- Estados conhecidos e previsíveis
- Não interfere com outros testes

## ⚠️ **Notas Importantes**

1. **Senhas Simples**: Apenas para desenvolvimento, não usar em produção
2. **Dados Fictícios**: CNPJ e dados são exemplos, não reais
3. **Reset Seguro**: `--fresh` remove permanentemente os dados
4. **Execução Idempotente**: Pode ser executado múltiplas vezes

## 📊 **Status dos Componentes**

- ✅ **TestUsersSeeder**: Funcional e testado
- ✅ **CreateTestUsers Command**: Interface completa
- ✅ **DatabaseSeeder Integration**: Automático
- ✅ **Dados Relacionais**: SellerProfile → User → Product
- ✅ **Remoção Segura**: Force delete funcionando
- ✅ **Documentação**: Completa e atualizada

---

**Criado em**: 31/08/2025  
**Última atualização**: 31/08/2025  
**Versão**: 1.0  
**Status**: Produção 🚀