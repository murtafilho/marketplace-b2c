# 🛒 Sistema de Carrinho de Compras - Marketplace B2C

**Data**: 03/01/2025  
**Versão**: 1.0  
**Status**: ✅ Funcional

## 📋 Visão Geral

O sistema de carrinho permite que usuários (logados ou visitantes) adicionem produtos, gerenciem quantidades e prossigam para o checkout. Suporta múltiplos vendedores em um único carrinho.

## 🏗️ Arquitetura

### Tabelas do Banco de Dados

#### 1. **carts**
```sql
- id (PK)
- user_id (FK) - NULL para visitantes
- session_id - Para visitantes
- total_amount - Total em R$
- total_items - Quantidade total
- shipping_data (JSON)
- coupon_data (JSON)
- last_activity
- expires_at
- created_at
- updated_at
```

#### 2. **cart_items**
```sql
- id (PK)
- cart_id (FK)
- product_id (FK)
- product_variation_id (FK) - Opcional
- quantity
- unit_price
- total_price
- product_snapshot (JSON)
- variation_snapshot (JSON)
- created_at
- updated_at
```

## 🔧 Funcionalidades Implementadas

### ✅ Funcionalidades Básicas
- Adicionar produtos ao carrinho
- Atualizar quantidade de itens
- Remover itens individuais
- Limpar carrinho completo
- Cálculo automático de totais
- Verificação de estoque em tempo real

### ✅ Funcionalidades Avançadas
- Suporte a usuários logados e visitantes
- Persistência de carrinho por sessão
- Snapshot de produtos (preserva preço no momento da adição)
- Botão "Comprar Agora" (adiciona e vai pro checkout)
- Suporte a variações de produtos
- Validação de produtos inativos

### ✅ Interface Responsiva
- Design mobile-first
- Controles touch-friendly
- Atualização AJAX sem reload
- Feedback visual de loading
- Estados vazios amigáveis

## 📱 Fluxos de Uso

### 1. **Visitante (Não Logado)**
```
1. Navega produtos → Adiciona ao carrinho
2. Carrinho salvo por session_id
3. Pode ajustar quantidades
4. Ao fazer login → carrinho preservado (TODO: merge)
```

### 2. **Usuário Logado**
```
1. Navega produtos → Adiciona ao carrinho
2. Carrinho salvo por user_id
3. Persiste entre sessões
4. Pode ter múltiplos carrinhos (histórico)
```

### 3. **Fluxo de Checkout**
```
1. Revisa itens no carrinho
2. Clica em "Finalizar Compra"
3. Sistema agrupa por vendedor
4. Calcula frete por vendedor
5. Processa pagamento único
6. Split automático para vendedores
```

## 🛠️ Controllers e Rotas

### CartController
```php
// Rotas implementadas
GET  /cart                 // Ver carrinho
POST /cart/add            // Adicionar item
PUT  /cart/update/{item}  // Atualizar quantidade
DELETE /cart/remove/{item} // Remover item
DELETE /cart/clear        // Limpar carrinho
```

### Métodos Principais
- `getCart()` - Obtém carrinho atual
- `getOrCreateCart()` - Cria se não existir
- `updateCartTotals()` - Recalcula totais
- `store()` - Adiciona produto
- `update()` - Atualiza quantidade
- `destroy()` - Remove item

## 🎨 Interface do Usuário

### Página do Carrinho
- **Desktop**: Layout em 2 colunas (itens + resumo)
- **Mobile**: Layout empilhado vertical
- **Componentes**:
  - Lista de itens com imagem
  - Controles de quantidade (+/-)
  - Botão remover item
  - Resumo com totais
  - Botão checkout
  - Avisos de estoque baixo

### JavaScript Functions
```javascript
updateQuantity(itemId, quantity) // Atualiza via AJAX
// Touch optimization para mobile
// Auto-select em inputs numéricos
```

## ⚠️ Validações e Regras de Negócio

### Validações Implementadas
1. **Estoque**: Não permite adicionar mais que disponível
2. **Produto Ativo**: Apenas produtos com status='active'
3. **Quantidade Mínima**: Sempre >= 1
4. **Propriedade**: Usuário só pode editar próprio carrinho
5. **Duplicados**: Soma quantidade se produto já existe

### Regras de Negócio
- Carrinho expira após 30 dias de inatividade
- Preço do produto é fixado no momento da adição
- Estoque é verificado em tempo real
- Múltiplos vendedores em um carrinho
- Sem limite de itens diferentes

## 🧪 Testes

### Testes Automatizados
```bash
php artisan test --filter=ShoppingCartTest
```

### Casos de Teste Cobertos
- ✅ Adicionar produto como visitante
- ✅ Adicionar produto como usuário logado
- ✅ Validação de estoque
- ✅ Atualizar quantidade
- ✅ Remover item
- ✅ Limpar carrinho
- ✅ Produtos inativos
- ✅ Botão "Comprar Agora"
- ✅ Soma de produtos duplicados

## 📊 Métricas e Performance

### Queries Otimizadas
```php
// Eager loading para evitar N+1
$cart->items()->with(['product.images', 'productVariation'])->get()
```

### Índices do Banco
- `carts`: user_id, session_id
- `cart_items`: cart_id, product_id

### Cache (TODO)
- Cache de totais do carrinho
- Cache de informações de produto

## 🚀 Como Usar

### 1. Adicionar ao Carrinho (Frontend)
```javascript
// Via formulário
<form method="POST" action="{{ route('cart.add') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="number" name="quantity" value="1">
    <button type="submit">Adicionar ao Carrinho</button>
</form>

// Comprar Agora
<input type="hidden" name="buy_now" value="1">
```

### 2. Atualizar Quantidade (AJAX)
```javascript
fetch('/cart/update/' + itemId, {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify({quantity: newQuantity})
});
```

## 🐛 Problemas Conhecidos

1. **Merge de Carrinho**: Quando visitante faz login, carrinho não é mesclado automaticamente
2. **Expiração**: Não há job para limpar carrinhos expirados
3. **Concorrência**: Possível race condition em alta demanda

## 📝 TODO - Melhorias Futuras

- [ ] Implementar merge de carrinho após login
- [ ] Job para limpar carrinhos abandonados
- [ ] Wishlist (lista de desejos)
- [ ] Salvar para depois
- [ ] Cupons de desconto
- [ ] Cálculo de frete no carrinho
- [ ] Recomendações baseadas no carrinho
- [ ] Notificação de itens com estoque baixo
- [ ] Histórico de carrinhos anteriores
- [ ] API REST para apps mobile

## 🔒 Segurança

- ✅ CSRF protection em todos os forms
- ✅ Validação de propriedade do carrinho
- ✅ Sanitização de inputs
- ✅ Verificação de autenticação onde necessário
- ✅ Rate limiting nas rotas de atualização

## 📞 Suporte

Para problemas com o carrinho:
1. Verificar logs: `storage/logs/laravel.log`
2. Limpar cache: `php artisan cache:clear`
3. Verificar sessão: `php artisan session:table`
