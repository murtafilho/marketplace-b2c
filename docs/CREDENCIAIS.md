# 🔑 Credenciais de Acesso - Marketplace B2C

## Links Diretos com Login Automático

### 👨‍💼 ADMINISTRADOR

**Link Direto com Credenciais:**
```
http://marketplace-b2c.test/login?email=admin@marketplace.com&password=admin123
```

- **Email:** admin@marketplace.com
- **Senha:** admin123
- **Dashboard:** http://marketplace-b2c.test/admin/dashboard

---

### 🏪 VENDEDORES APROVADOS

#### Tech Store Brasil (Principal)
**Link Direto:**
```
http://marketplace-b2c.test/login?email=tech@marketplace.com&password=seller123
```
- **Email:** tech@marketplace.com
- **Senha:** seller123
- **Empresa:** Tech Store Brasil Ltda
- **Dashboard:** http://marketplace-b2c.test/seller/dashboard

#### Fashion House
**Link Direto:**
```
http://marketplace-b2c.test/login?email=fashion@marketplace.com&password=seller123
```
- **Email:** fashion@marketplace.com
- **Senha:** seller123
- **Empresa:** Fashion House Moda Ltda

#### Casa & Decoração
**Link Direto:**
```
http://marketplace-b2c.test/login?email=casa@marketplace.com&password=seller123
```
- **Email:** casa@marketplace.com
- **Senha:** seller123
- **Empresa:** Casa & Decoração Eireli

#### Sports Center
**Link Direto:**
```
http://marketplace-b2c.test/login?email=sports@marketplace.com&password=seller123
```
- **Email:** sports@marketplace.com
- **Senha:** seller123
- **Empresa:** Sports Center Equipamentos

#### Beauty World
**Link Direto:**
```
http://marketplace-b2c.test/login?email=beauty@marketplace.com&password=seller123
```
- **Email:** beauty@marketplace.com
- **Senha:** seller123
- **Empresa:** Beauty World Cosméticos

#### Livraria Digital
**Link Direto:**
```
http://marketplace-b2c.test/login?email=livros@marketplace.com&password=seller123
```
- **Email:** livros@marketplace.com
- **Senha:** seller123
- **Empresa:** Livraria Digital Educação

#### Game Station
**Link Direto:**
```
http://marketplace-b2c.test/login?email=games@marketplace.com&password=seller123
```
- **Email:** games@marketplace.com
- **Senha:** seller123
- **Empresa:** Game Station Entretenimento

#### Auto Peças Express
**Link Direto:**
```
http://marketplace-b2c.test/login?email=auto@marketplace.com&password=seller123
```
- **Email:** auto@marketplace.com
- **Senha:** seller123
- **Empresa:** Auto Peças Express Ltda

---

### 🔄 VENDEDORES PARA TESTES DE FLUXO

#### Pendente de Aprovação
**Link Direto:**
```
http://marketplace-b2c.test/login?email=pendente@marketplace.com&password=seller123
```
- **Email:** pendente@marketplace.com
- **Senha:** seller123
- **Status:** ⏳ Aguardando aprovação
- **Uso:** Testar fluxo de aprovação

#### Rejeitado
**Link Direto:**
```
http://marketplace-b2c.test/login?email=rejeitado@marketplace.com&password=seller123
```
- **Email:** rejeitado@marketplace.com
- **Senha:** seller123
- **Status:** ❌ Rejeitado
- **Uso:** Testar tela de rejeição

---

### 🛒 CLIENTES/CUSTOMERS

#### Cliente 1 (Principal para testes)
**Link Direto:**
```
http://marketplace-b2c.test/login?email=cliente1@marketplace.com&password=cliente123
```
- **Email:** cliente1@marketplace.com
- **Senha:** cliente123
- **Status:** ✅ Verificado

#### Cliente 2
**Link Direto:**
```
http://marketplace-b2c.test/login?email=cliente2@marketplace.com&password=cliente123
```
- **Email:** cliente2@marketplace.com
- **Senha:** cliente123

#### Cliente 3
**Link Direto:**
```
http://marketplace-b2c.test/login?email=cliente3@marketplace.com&password=cliente123
```
- **Email:** cliente3@marketplace.com
- **Senha:** cliente123

#### Cliente 4
**Link Direto:**
```
http://marketplace-b2c.test/login?email=cliente4@marketplace.com&password=cliente123
```
- **Email:** cliente4@marketplace.com
- **Senha:** cliente123

#### Cliente 5
**Link Direto:**
```
http://marketplace-b2c.test/login?email=cliente5@marketplace.com&password=cliente123
```
- **Email:** cliente5@marketplace.com
- **Senha:** cliente123

---

## 📝 Instruções de Uso

### Para usar os links diretos:

1. **Copie o link desejado** da seção apropriada
2. **Cole no navegador** e pressione Enter
3. **As credenciais serão preenchidas automaticamente**
4. **Clique em "Entrar"** para fazer login

### Navegação Rápida:

- **Home do Marketplace:** http://marketplace-b2c.test/
- **Página de Login:** http://marketplace-b2c.test/login
- **Página de Registro:** http://marketplace-b2c.test/register

---

## 🧪 Cenários de Teste Recomendados

### Como Admin:
1. ✅ Aprovar seller pendente
2. ✅ Gerenciar produtos de sellers
3. ✅ Visualizar estatísticas
4. ✅ Gerenciar categorias

### Como Seller:
1. ✅ Criar novos produtos
2. ✅ Gerenciar estoque
3. ✅ Visualizar pedidos
4. ✅ Atualizar perfil da loja

### Como Customer:
1. ✅ Navegar por categorias
2. ✅ Adicionar ao carrinho
3. ✅ Finalizar compra
4. ✅ Gerenciar perfil

---

## ⚠️ Importante

- **Todos os links funcionam apenas no ambiente local:** `marketplace-b2c.test`
- **Certifique-se que o Laragon está rodando** antes de usar os links
- **As senhas são simples para facilitar os testes:** não use em produção
- **Alguns emails podem não estar verificados:** teste ambos os cenários

---

## 📊 Resumo Rápido

| Tipo | Quantidade | Senha Padrão |
|------|------------|--------------|
| Admin | 1 | admin123 |
| Sellers | 10 | seller123 |
| Customers | 20 | cliente123 |

**Total de usuários:** 31
**Status:** ✅ Todos funcionais para testes

---

*Links gerados automaticamente para facilitar os testes*  
*Ambiente: marketplace-b2c.test*  
*Data: 28/08/2025*