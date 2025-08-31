# Sistema de Imagens de Categorias

## Visão Geral

Sistema completo para gerenciamento de imagens de categorias usando APIs externas de bancos de imagens gratuitos de alta qualidade.

## 🚀 Recursos

- **Download automático** de imagens via APIs (Unsplash, Pexels)
- **Placeholders dinâmicos** quando APIs não estão disponíveis
- **Metadados completos** para conformidade legal
- **Otimização automática** de imagens
- **Fallbacks inteligentes** para máxima confiabilidade

## 📋 Configuração

### 1. Variáveis de Ambiente

Adicione ao seu arquivo `.env`:

```env
# Unsplash (Recomendado - Melhor qualidade)
UNSPLASH_ACCESS_KEY=sua_chave_aqui

# Pexels (Alternativa/Backup)
PEXELS_API_KEY=sua_chave_aqui
```

### 2. Obtenção das Chaves

**Unsplash:**
1. Acesse: https://unsplash.com/developers
2. Crie uma conta
3. Crie um app
4. Copie a "Access Key"

**Pexels:**
1. Acesse: https://www.pexels.com/api/
2. Crie uma conta
3. Obtenha a API Key

### 3. Configuração do Storage

Certifique-se de que o link simbólico do storage existe:

```bash
php artisan storage:link
```

## 🛠️ Uso

### Comandos Artisan

#### Download de Imagens
```bash
# Baixar imagens para todas as categorias sem imagem
php artisan categories:download-images

# Forçar download para todas as categorias (substitui existentes)
php artisan categories:download-images --force

# Baixar apenas para uma categoria específica
php artisan categories:download-images --category=eletronicos

# Limitar número de downloads por execução
php artisan categories:download-images --limit=5
```

#### Imagens Placeholder (Sistema Anterior)
```bash
# Criar placeholders coloridos para categorias sem imagem
php artisan categories:create-images
```

### Uso Programático

#### CategoryImageService

```php
use App\Services\CategoryImageService;

// Injeção de dependência
public function __construct(CategoryImageService $imageService)
{
    $this->imageService = $imageService;
}

// Baixar imagem de alta qualidade
$imagePath = $this->imageService->downloadHighQualityImage('eletronicos');

// URL com fallback automático
$imageUrl = $this->imageService->getImageUrlWithFallback(
    $category->image_path, 
    $category->slug
);

// Obter metadados da imagem
$metadata = $this->imageService->getImageMetadata('eletronicos');

// Gerar atribuição legal
$attribution = $this->imageService->getImageAttribution('eletronicos');

// Otimizar imagem existente
$this->imageService->optimizeImage($category->image_path);
```

#### ImageDownloadService

```php
use App\Services\ImageDownloadService;

// Download direto
$downloadService = app(ImageDownloadService::class);
$imagePath = $downloadService->downloadCategoryImage('fashion');

// Placeholder se download falhar
$placeholderUrl = $downloadService->generatePlaceholder('fashion');
```

## 🎨 Templates Blade

### Exibindo Imagens com Fallback

```blade
{{-- resources/views/components/category-image.blade.php --}}
@props(['category', 'size' => 'medium'])

@php
    $imageService = app(\App\Services\CategoryImageService::class);
    $imageUrl = $imageService->getImageUrlWithFallback(
        $category->image_path, 
        $category->slug
    );
    
    $classes = [
        'small' => 'w-16 h-16',
        'medium' => 'w-32 h-32', 
        'large' => 'w-64 h-64'
    ];
@endphp

<div class="relative {{ $classes[$size] ?? $classes['medium'] }}">
    <img 
        src="{{ $imageUrl }}" 
        alt="{{ $category->name }}"
        class="w-full h-full object-cover rounded-lg"
        loading="lazy"
        onerror="this.src='https://via.placeholder.com/600x600/f8f9fa/6c757d?text=Categoria'"
    >
</div>
```

### Atribuições para Conformidade Legal

```blade
{{-- resources/views/components/image-credits.blade.php --}}
@php
    $imageService = app(\App\Services\CategoryImageService::class);
@endphp

<div class="text-xs text-gray-500 mt-4">
    <h4 class="font-medium mb-2">Créditos das Imagens:</h4>
    @foreach($categories as $category)
        @php
            $attribution = $imageService->getImageAttribution($category->slug);
        @endphp
        
        @if($attribution)
            <p class="mb-1">
                <strong>{{ $category->name }}:</strong> 
                {!! $attribution !!}
            </p>
        @endif
    @endforeach
</div>
```

## 📊 Estrutura de Metadados

Os metadados são salvos em `storage/app/image_metadata/{categoria}.json`:

```json
{
    "path": "categories/category_eletronicos.jpg",
    "category": "eletronicos",
    "source": "unsplash",
    "source_url": "https://unsplash.com/photos/abc123",
    "photographer": "John Doe",
    "photographer_url": "https://unsplash.com/@johndoe",
    "downloaded_at": "2025-08-30T22:55:00.000Z"
}
```

## 🔄 Mapeamento de Categorias

O sistema possui mapeamento inteligente de categorias brasileiras para termos em inglês otimizados para busca:

```php
'eletronicos' => 'electronics',
'smartphones-e-celulares' => 'smartphone',
'roupas-femininas' => 'women fashion',
'casa-e-jardim' => 'home decor',
// ... e muitos outros
```

## 🛡️ Conformidade Legal

### Licenças Suportadas
- **Unsplash**: Unsplash License (uso comercial permitido)
- **Pexels**: Pexels License (uso comercial livre)

### Boas Práticas
1. ✅ Sempre salvar metadados
2. ✅ Registrar downloads no Unsplash (para estatísticas)
3. ✅ Fornecer atribuições quando necessário
4. ✅ Evitar imagens com pessoas identificáveis
5. ✅ Documentar origem de cada imagem

## 🔧 Troubleshooting

### Problema: "Nenhuma chave de API configurada"
**Solução:** Configure pelo menos uma das chaves no `.env`

### Problema: "Não foi possível baixar imagem"
**Possíveis causas:**
- Conexão com internet
- Limite de API atingido
- Chave de API inválida

**Soluções:**
1. Verificar conexão
2. Verificar logs em `storage/logs/laravel.log`
3. Tentar novamente mais tarde
4. Usar `--force` para forçar re-download

### Problema: Imagens não aparecem
**Soluções:**
1. Verificar se o storage link existe: `php artisan storage:link`
2. Verificar permissões da pasta `storage/app/public`
3. Verificar configuração do servidor web

## 📈 Performance

### Otimizações Implementadas
- **Compressão JPEG** com qualidade 85%
- **Lazy loading** nos templates
- **Fallbacks** para evitar imagens quebradas
- **Cache** de metadados em arquivos JSON
- **Timeouts** configurados para APIs

### Recomendações
- Execute downloads em horários de baixo tráfego
- Use `--limit` para controlar carga do servidor
- Monitore logs para identificar problemas
- Configure CDN para servir imagens (opcional)

## 🔄 Migrações Futuras

Para adicionar suporte a novos serviços, edite:
- `ImageDownloadService::searchNewService()`
- Configurações em `config/services.php`
- Mapeamentos de categoria conforme necessário

## 📝 Log de Alterações

- **v1.0** - Sistema básico com placeholders GD
- **v2.0** - Integração com Unsplash e Pexels
- **v2.1** - Metadados e conformidade legal
- **v2.2** - Otimização automática de imagens