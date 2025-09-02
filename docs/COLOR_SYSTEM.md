# Sistema de Cores - Marketplace B2C

## 🎨 Paleta de Cores e Uso Semântico (ATUALIZADA)

### **1. Cores Principais (Emerald = Azul)**
Azul - Nova identidade principal do marketplace
```css
/* Uso: Ações principais, CTAs, elementos de marca */
bg-emerald-50   /* Azul muito sutil - fundos */
bg-emerald-100  /* Azul claro - fundos de seções */
bg-emerald-200  /* Azul suave - borders leves */
bg-emerald-400  /* Azul vibrante - estados hover */
bg-emerald-500  /* Azul padrão - links ativos */
bg-emerald-600  /* Azul forte - botões principais */
bg-emerald-700  /* Azul escuro - hover em botões */
bg-emerald-800  /* Azul profundo - headers importantes */
```

### **2. Laranja Informativo (Info)**
Laranja para informações, destaques e elementos secundários
```css
/* Uso: Informações, badges, alertas informativos, links secundários */
bg-info-50      /* Laranja muito claro - fundos de cards informativos */
bg-info-100     /* Laranja claro - hover states sutis */
bg-info-200     /* Laranja suave - borders de elementos info */
bg-info-300     /* Laranja médio - ícones informativos */
bg-info-400     /* Laranja vibrante - links secundários */
bg-info-500     /* Laranja padrão - botões informativos */
bg-info-600     /* Laranja forte - CTAs secundários, headers */
bg-info-700     /* Laranja escuro - estados hover fortes */
bg-info-800     /* Laranja profundo - textos de destaque */
bg-info-900     /* Laranja muito escuro - headers secundários */
```

### **3. Estados e Feedback**
```css
/* Sucesso */
bg-success-100  /* Fundo de mensagens de sucesso */
text-success-600 /* Texto de sucesso */

/* Aviso */
bg-warning-100  /* Fundo de avisos */
text-warning-600 /* Texto de aviso */

/* Erro */
bg-danger-100   /* Fundo de erros */
text-danger-600  /* Texto de erro */
```

## 📋 Guia de Uso Semântico

### **Botões**
```html
<!-- Ação Principal (Comprar, Adicionar ao Carrinho) - AZUL -->
<button class="bg-emerald-600 hover:bg-emerald-700 text-white">
  Comprar Agora
</button>

<!-- Ação Secundária (Ver Detalhes, Mais Informações) - LARANJA -->
<button class="bg-info-500 hover:bg-info-600 text-white">
  Ver Detalhes
</button>

<!-- Ação Terciária (Cancelar, Voltar) -->
<button class="bg-gray-200 hover:bg-gray-300 text-gray-700">
  Cancelar
</button>
```

### **Cards e Seções**
```html
<!-- Card de Produto (Principal) -->
<div class="bg-white border-emerald-200 hover:border-emerald-400">
  <!-- conteúdo -->
</div>

<!-- Card Informativo -->
<div class="bg-info-50 border-info-200">
  <h3 class="text-info-800">Informação Importante</h3>
  <p class="text-info-600">Detalhes...</p>
</div>

<!-- Card de Destaque/Promoção -->
<div class="bg-gradient-to-r from-emerald-50 to-info-50">
  <!-- conteúdo promocional -->
</div>
```

### **Badges e Labels**
```html
<!-- Badge de Novo -->
<span class="bg-emerald-100 text-emerald-800">Novo</span>

<!-- Badge de Informação -->
<span class="bg-info-100 text-info-800">Info</span>

<!-- Badge de Desconto -->
<span class="bg-warning-100 text-warning-800">-20%</span>

<!-- Badge de Destaque -->
<span class="bg-info-500 text-white">Destaque</span>
```

### **Links e Navegação**
```html
<!-- Link Principal - AZUL -->
<a href="#" class="text-emerald-600 hover:text-emerald-700">
  Link Principal
</a>

<!-- Link Secundário/Informativo - LARANJA -->
<a href="#" class="text-info-600 hover:text-info-700">
  Mais Informações
</a>

<!-- Link Neutro -->
<a href="#" class="text-gray-600 hover:text-gray-800">
  Link Regular
</a>
```

### **Formulários**
```html
<!-- Input com Foco Principal -->
<input class="border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">

<!-- Input com Foco Informativo -->
<input class="border-gray-300 focus:border-info-500 focus:ring-info-500">

<!-- Select/Dropdown -->
<select class="border-gray-300 focus:border-emerald-500">
  <option>Opção</option>
</select>
```

## 🎯 Casos de Uso Específicos

### **1. Hero/Banner**
- Fundo: `bg-gradient-to-r from-emerald-600 to-info-600`
- Texto: `text-white`
- CTA Principal: `bg-white text-emerald-600`
- CTA Secundário: `bg-info-100 text-info-700`

### **2. Navbar**
- Fundo: `bg-white` ou `bg-gray-50`
- Links: `text-gray-700 hover:text-emerald-600`
- Link Ativo: `text-emerald-600`
- Botão CTA: `bg-info-500 text-white`

### **3. Cards de Produto**
- Border: `border-gray-200 hover:border-info-300`
- Preço: `text-emerald-600 font-bold`
- Botão: `bg-emerald-600 hover:bg-emerald-700`
- Badge Novo: `bg-info-100 text-info-800`

### **4. Footer**
- Fundo: `bg-gray-900`
- Títulos: `text-white`
- Links: `text-gray-400 hover:text-info-400`
- Ícones Sociais: `text-gray-400 hover:text-emerald-400`

## 🔄 Transições e Animações

```html
<!-- Transição suave de cores -->
<button class="transition-colors duration-200 bg-emerald-600 hover:bg-emerald-700">
  Botão
</button>

<!-- Card com hover elevado -->
<div class="transition-all duration-300 hover:shadow-lg hover:border-info-400">
  Card
</div>

<!-- Link com underline animado -->
<a class="relative text-info-600 after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-info-600 after:transition-all hover:after:w-full">
  Link Animado
</a>
```

## ⚡ Melhores Práticas

1. **Hierarquia Visual**
   - Verde Esmeralda: Ações principais, elementos de marca
   - Azul Info: Informações, ações secundárias
   - Cinza: Elementos neutros, textos secundários

2. **Acessibilidade**
   - Sempre manter contraste WCAG AA (4.5:1 para texto normal)
   - Usar `hover:` e `focus:` states para interatividade
   - Incluir `focus-visible:` para navegação por teclado

3. **Consistência**
   - Usar sempre a mesma cor para a mesma ação
   - Manter padrões através de toda a aplicação
   - Documentar novos usos de cores

4. **Performance**
   - Usar classes Tailwind nativas quando possível
   - Evitar cores customizadas inline
   - Aproveitar o purge do Tailwind para CSS otimizado