# 🎨 **Guia de Fontes - Marketplace B2C**

## 📋 **Fontes Instaladas via Fontsource**

### **✅ Fontes Disponíveis:**
- **Inter** - Fonte principal (sans-serif moderna)
- **Poppins** - Fonte display (títulos e destaque)  
- **Roboto** - Fonte complementar (interface)

## 🎯 **Como Usar no Tailwind CSS**

### **1. Classes de Font Family:**
```html
<!-- Fonte padrão (Inter) -->
<p class="font-sans">Texto com Inter</p>

<!-- Fonte display (Poppins) -->
<h1 class="font-display">Título com Poppins</h1>

<!-- Fonte Roboto -->
<span class="font-roboto">Interface com Roboto</span>
```

### **2. Pesos Disponíveis:**

#### **Inter (font-sans):**
```html
<p class="font-sans font-normal">Regular (400)</p>
<p class="font-sans font-medium">Medium (500)</p>
<p class="font-sans font-semibold">Semibold (600)</p>
<p class="font-sans font-bold">Bold (700)</p>
```

#### **Poppins (font-display):**
```html
<h1 class="font-display font-normal">Regular (400)</h1>
<h1 class="font-display font-medium">Medium (500)</h1>
<h1 class="font-display font-semibold">Semibold (600)</h1>
<h1 class="font-display font-bold">Bold (700)</h1>
```

#### **Roboto (font-roboto):**
```html
<span class="font-roboto font-light">Light (300)</span>
<span class="font-roboto font-normal">Regular (400)</span>
<span class="font-roboto font-medium">Medium (500)</span>
<span class="font-roboto font-bold">Bold (700)</span>
```

## 🏗️ **Configuração Técnica**

### **Arquivos Modificados:**
- **`resources/css/app.css`**: Imports das fontes
- **`tailwind.config.js`**: Definição das font families

### **Imports no CSS:**
```css
/* Inter */
@import '@fontsource/inter/400.css';  /* Regular */
@import '@fontsource/inter/500.css';  /* Medium */
@import '@fontsource/inter/600.css';  /* Semibold */
@import '@fontsource/inter/700.css';  /* Bold */

/* Roboto */
@import '@fontsource/roboto/300.css'; /* Light */
@import '@fontsource/roboto/400.css'; /* Regular */
@import '@fontsource/roboto/500.css'; /* Medium */
@import '@fontsource/roboto/700.css'; /* Bold */

/* Poppins */
@import '@fontsource/poppins/400.css'; /* Regular */
@import '@fontsource/poppins/500.css'; /* Medium */
@import '@fontsource/poppins/600.css'; /* Semibold */
@import '@fontsource/poppins/700.css'; /* Bold */
```

## 🎨 **Recomendações de Uso**

### **Hierarquia de Fontes:**

1. **Títulos Principais** → `font-display` (Poppins)
   ```html
   <h1 class="font-display font-bold text-4xl">Título Principal</h1>
   ```

2. **Texto de Interface** → `font-sans` (Inter)
   ```html
   <p class="font-sans font-normal">Texto de conteúdo</p>
   <button class="font-sans font-medium">Botão</button>
   ```

3. **Labels e Metadados** → `font-roboto` (Roboto)
   ```html
   <span class="font-roboto font-light text-sm">Labels pequenas</span>
   ```

### **Exemplos Práticos:**

#### **Card de Produto:**
```html
<div class="bg-white rounded-lg p-6">
    <!-- Nome do produto -->
    <h3 class="font-display font-semibold text-xl mb-2">Nome Produto</h3>
    
    <!-- Descrição -->
    <p class="font-sans font-normal text-gray-600 mb-4">Descrição do produto...</p>
    
    <!-- Preço -->
    <div class="font-sans font-bold text-2xl text-green-600">R$ 99,90</div>
    
    <!-- Metadata -->
    <span class="font-roboto font-light text-xs text-gray-500">por Vendedor XYZ</span>
</div>
```

#### **Header de Seção:**
```html
<section class="py-12">
    <div class="text-center mb-8">
        <h2 class="font-display font-bold text-4xl text-verde-mata mb-2">
            Produtos em Destaque
        </h2>
        <p class="font-sans font-normal text-lg text-cinza-pedra">
            O melhor da nossa comunidade
        </p>
    </div>
</section>
```

## ⚡ **Performance**

### **Vantagens do Fontsource:**
- ✅ **Self-hosted** (não depende de CDN)
- ✅ **Controle total** sobre pesos e estilos
- ✅ **Performance otimizada** (apenas pesos necessários)
- ✅ **Cache local** (funciona offline)
- ✅ **GDPR compliant** (não envia dados para Google)

### **Build Otimizado:**
- Apenas pesos utilizados são incluídos no bundle
- Formato WOFF2 para melhor compressão
- Suporte a múltiplos idiomas (Latin, Cyrillic, etc.)

## 🔄 **Migração das Fontes Antigas**

Se houver fontes antigas no projeto, substituir gradualmente:

```html
<!-- ❌ Antes -->
<h1 class="text-4xl font-bold">Título</h1>

<!-- ✅ Depois -->
<h1 class="font-display font-bold text-4xl">Título</h1>
```

## 📦 **Comandos Úteis**

```bash
# Recompilar após mudanças
npm run build

# Desenvolvimento com hot reload
npm run dev

# Verificar tamanho do bundle
npm run build && ls -lah public/build/assets/
```

---

**💡 Dica:** Sempre usar `font-display` para títulos e `font-sans` para texto corrido para manter consistência visual.