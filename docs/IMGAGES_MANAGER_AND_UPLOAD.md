Análise de Stack para Gerenciamento de Imagens - Marketplace B2C
📸 Recomendação Principal: Spatie Media Library
Para o seu projeto Laravel 12.x, a melhor escolha é Spatie Media Library pelos seguintes motivos:
Por que Spatie Media Library?

Integração nativa com Laravel - Já mencionado no PROJECT-SPECS.md
Conversões automáticas - Gera thumbnails e variações automaticamente
Otimização built-in - Compressão e otimização de imagens
S3/Local storage - Suporte para múltiplos drivers
Responsivo - Gera múltiplas resoluções para srcset

🔧 Implementação Completa
1. Instalação e Configuração
bash# Arquivo: terminal
# Descrição: Instalação dos pacotes necessários

composer require spatie/laravel-medialibrary
composer require intervention/image
npm install filepond filepond-plugin-image-preview filepond-plugin-file-validate-type
2. Model de Produto com Media
php<?php
// Arquivo: app/Models/Product.php
// Descrição: Model com integração Spatie Media Library

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail para listagem
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();
            
        // Imagem média para cards
        $this->addMediaConversion('preview')
            ->width(600)
            ->height(600)
            ->quality(85)
            ->optimize();
            
        // Imagem grande para zoom
        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->quality(90)
            ->optimize();
    }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('products')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public')
            ->singleFile(); // Para imagem principal
            
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }
}
3. Componente Upload Interativo com FilePond
blade{{-- Arquivo: resources/views/components/image-upload.blade.php --}}
{{-- Descrição: Componente de upload com preview interativo --}}

<div x-data="imageUploader(@js($config))" 
     x-init="init()"
     class="w-full">
    
    {{-- Drop Zone --}}
    <div class="relative">
        <input type="file" 
               x-ref="fileInput"
               :accept="acceptedTypes"
               :multiple="multiple"
               class="hidden">
        
        {{-- Preview Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
            <template x-for="(image, index) in images" :key="image.id">
                <div class="relative group aspect-square">
                    {{-- Imagem --}}
                    <img :src="image.url" 
                         :alt="image.name"
                         class="w-full h-full object-cover rounded-lg">
                    
                    {{-- Overlay de ações --}}
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 
                                group-hover:opacity-100 transition-opacity rounded-lg
                                flex items-center justify-center space-x-2">
                        
                        {{-- Botão Editar --}}
                        <button @click="editImage(index)"
                                type="button"
                                class="p-2 bg-white rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-700">
                                <!-- edit icon -->
                            </svg>
                        </button>
                        
                        {{-- Botão Preview --}}
                        <button @click="previewImage(index)"
                                type="button"
                                class="p-2 bg-white rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5 text-gray-700">
                                <!-- eye icon -->
                            </svg>
                        </button>
                        
                        {{-- Botão Remover --}}
                        <button @click="removeImage(index)"
                                type="button"
                                class="p-2 bg-red-500 rounded-full hover:bg-red-600">
                            <svg class="w-5 h-5 text-white">
                                <!-- trash icon -->
                            </svg>
                        </button>
                    </div>
                    
                    {{-- Badge Principal --}}
                    <div x-show="index === 0"
                         class="absolute top-2 left-2 px-2 py-1 bg-blue-500 
                                text-white text-xs rounded">
                        Principal
                    </div>
                    
                    {{-- Loading --}}
                    <div x-show="image.uploading"
                         class="absolute inset-0 bg-white bg-opacity-75 
                                flex items-center justify-center rounded-lg">
                        <div class="w-8 h-8 border-4 border-blue-500 
                                    border-t-transparent rounded-full animate-spin">
                        </div>
                    </div>
                </div>
            </template>
            
            {{-- Botão Adicionar --}}
            <button @click="$refs.fileInput.click()"
                    type="button"
                    class="aspect-square border-2 border-dashed border-gray-300 
                           rounded-lg hover:border-blue-500 transition-colors
                           flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-gray-400 mb-2">
                    <!-- plus icon -->
                </svg>
                <span class="text-sm text-gray-500">Adicionar</span>
                <span class="text-xs text-gray-400 mt-1">
                    Máx {{ maxFiles }} imagens
                </span>
            </button>
        </div>
    </div>
    
    {{-- Modal de Preview --}}
    <div x-show="showPreview"
         x-transition
         @click.away="closePreview()"
         class="fixed inset-0 z-50 flex items-center justify-center 
                bg-black bg-opacity-75">
        <div class="max-w-4xl max-h-[90vh] overflow-auto">
            <img :src="previewUrl" 
                 class="w-auto h-auto max-w-full max-h-full">
        </div>
    </div>
    
    {{-- Editor de Imagem --}}
    <div x-show="showEditor"
         x-transition
         class="fixed inset-0 z-50 bg-white">
        <div class="h-full flex flex-col">
            {{-- Toolbar --}}
            <div class="border-b p-4 flex items-center justify-between">
                <div class="flex space-x-2">
                    <button @click="cropImage()" 
                            class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                        Cortar
                    </button>
                    <button @click="rotateImage()" 
                            class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                        Girar
                    </button>
                    <button @click="flipImage()" 
                            class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">
                        Espelhar
                    </button>
                </div>
                
                <div class="flex space-x-2">
                    <button @click="cancelEdit()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancelar
                    </button>
                    <button @click="saveEdit()" 
                            class="px-4 py-2 bg-blue-500 text-white rounded 
                                   hover:bg-blue-600">
                        Salvar
                    </button>
                </div>
            </div>
            
            {{-- Canvas de Edição --}}
            <div class="flex-1 p-4">
                <canvas x-ref="editCanvas"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
function imageUploader(config = {}) {
    return {
        images: [],
        maxFiles: config.maxFiles || 5,
        acceptedTypes: config.acceptedTypes || 'image/*',
        multiple: config.multiple ?? true,
        showPreview: false,
        showEditor: false,
        previewUrl: null,
        editingIndex: null,
        
        init() {
            // Configurar FilePond ou listeners
            this.$refs.fileInput.addEventListener('change', (e) => {
                this.handleFiles(e.target.files);
            });
            
            // Drag and drop
            this.setupDragDrop();
        },
        
        async handleFiles(files) {
            for (let file of files) {
                if (this.images.length >= this.maxFiles) {
                    this.$dispatch('notify', {
                        type: 'warning',
                        message: `Máximo de ${this.maxFiles} imagens permitidas`
                    });
                    break;
                }
                
                await this.uploadFile(file);
            }
        },
        
        async uploadFile(file) {
            // Validar tipo e tamanho
            if (!this.validateFile(file)) return;
            
            // Criar preview local
            const localUrl = URL.createObjectURL(file);
            const imageId = Date.now() + Math.random();
            
            this.images.push({
                id: imageId,
                url: localUrl,
                name: file.name,
                size: file.size,
                uploading: true
            });
            
            // Upload para servidor
            try {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('collection', 'products');
                
                const response = await fetch('/api/upload/image', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                // Atualizar com URL do servidor
                const index = this.images.findIndex(img => img.id === imageId);
                if (index !== -1) {
                    this.images[index] = {
                        ...this.images[index],
                        url: data.url,
                        thumbnailUrl: data.thumbnailUrl,
                        serverPath: data.path,
                        uploading: false
                    };
                }
                
            } catch (error) {
                console.error('Upload failed:', error);
                this.removeImage(this.images.findIndex(img => img.id === imageId));
                
                this.$dispatch('notify', {
                    type: 'error',
                    message: 'Erro ao fazer upload da imagem'
                });
            }
        },
        
        validateFile(file) {
            // Tamanho máximo: 5MB
            if (file.size > 5 * 1024 * 1024) {
                this.$dispatch('notify', {
                    type: 'error',
                    message: 'Imagem muito grande. Máximo 5MB.'
                });
                return false;
            }
            
            // Tipo
            if (!file.type.startsWith('image/')) {
                this.$dispatch('notify', {
                    type: 'error',
                    message: 'Apenas imagens são permitidas.'
                });
                return false;
            }
            
            return true;
        },
        
        editImage(index) {
            this.editingIndex = index;
            this.showEditor = true;
            this.$nextTick(() => {
                this.initializeEditor();
            });
        },
        
        initializeEditor() {
            // Inicializar Cropper.js ou similar
            const canvas = this.$refs.editCanvas;
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = () => {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
            };
            
            img.src = this.images[this.editingIndex].url;
        },
        
        removeImage(index) {
            if (confirm('Remover esta imagem?')) {
                this.images.splice(index, 1);
            }
        },
        
        previewImage(index) {
            this.previewUrl = this.images[index].url;
            this.showPreview = true;
        },
        
        closePreview() {
            this.showPreview = false;
            this.previewUrl = null;
        }
    }
}
</script>
4. Controller de Upload
php<?php
// Arquivo: app/Http/Controllers/Api/ImageUploadController.php
// Descrição: Controller para gerenciar uploads de imagens

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
            'collection' => 'required|string'
        ]);
        
        $file = $request->file('image');
        
        // Otimizar imagem
        $image = Image::make($file);
        
        // Redimensionar se muito grande
        if ($image->width() > 2400 || $image->height() > 2400) {
            $image->resize(2400, 2400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Converter para WebP se suportado
        if ($request->header('Accept') && str_contains($request->header('Accept'), 'image/webp')) {
            $image->encode('webp', 85);
            $extension = 'webp';
        } else {
            $image->encode('jpg', 85);
            $extension = 'jpg';
        }
        
        // Salvar
        $path = 'products/' . uniqid() . '.' . $extension;
        Storage::disk('public')->put($path, $image->stream());
        
        // Gerar thumbnail
        $thumbnail = Image::make($file)
            ->fit(300, 300)
            ->encode($extension, 80);
        
        $thumbPath = 'products/thumbs/' . basename($path);
        Storage::disk('public')->put($thumbPath, $thumbnail->stream());
        
        return response()->json([
            'url' => Storage::url($path),
            'thumbnailUrl' => Storage::url($thumbPath),
            'path' => $path,
            'size' => $image->filesize(),
            'dimensions' => [
                'width' => $image->width(),
                'height' => $image->height()
            ]
        ]);
    }
}
🗄️ Análise do Banco de Dados
✅ Pontos Positivos

Estrutura normalizada - Boa separação de tabelas
Índices apropriados - Bem otimizado para queries
Soft deletes - Implementado onde necessário
JSON fields - Uso adequado para dados flexíveis

⚠️ Melhorias Necessárias
1. Tabela de Imagens Melhorada
sql-- Arquivo: database/migrations/improve_product_images_table.php
-- Descrição: Melhorias na tabela de imagens

ALTER TABLE product_images ADD COLUMN cdn_url VARCHAR(500) AFTER file_path;
ALTER TABLE product_images ADD COLUMN blurhash VARCHAR(100) AFTER alt_text;
ALTER TABLE product_images ADD COLUMN dominant_color VARCHAR(7) AFTER blurhash;
ALTER TABLE product_images ADD COLUMN format VARCHAR(10) AFTER mime_type;
ALTER TABLE product_images ADD COLUMN optimization_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending';

-- Índice para busca por status de otimização
CREATE INDEX idx_optimization_status ON product_images(optimization_status, created_at);
2. Tabela de Cache de Imagens
sql-- Arquivo: database/migrations/create_image_cache_table.php
-- Descrição: Cache para conversões de imagens

CREATE TABLE image_conversions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_image_id BIGINT NOT NULL,
    conversion_name VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    cdn_url VARCHAR(500),
    width INT,
    height INT,
    file_size INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_conversion (product_image_id, conversion_name),
    FOREIGN KEY (product_image_id) REFERENCES product_images(id) ON DELETE CASCADE,
    INDEX idx_conversion_name (conversion_name)
);
🚀 Sistema de Otimização
1. Queue para Processamento
php<?php
// Arquivo: app/Jobs/OptimizeProductImage.php
// Descrição: Job para otimização assíncrona de imagens

namespace App\Jobs;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class OptimizeProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function handle()
    {
        $optimizerChain = OptimizerChainFactory::create();
        
        // Otimizar original
        $optimizerChain->optimize($this->imagePath);
        
        // Gerar WebP
        $this->generateWebP();
        
        // Gerar AVIF (se suportado)
        $this->generateAVIF();
        
        // Calcular BlurHash para placeholder
        $this->generateBlurHash();
        
        // Upload para CDN
        $this->uploadToCDN();
    }
    
    private function generateBlurHash()
    {
        $image = Image::make($this->imagePath);
        $resized = $image->fit(32, 32);
        
        // Usar biblioteca kornrunner/blurhash
        $hash = Blurhash::encode($resized);
        
        $this->productImage->update(['blurhash' => $hash]);
    }
}
2. Componente de Imagem Otimizada
blade{{-- Arquivo: resources/views/components/optimized-image.blade.php --}}
{{-- Descrição: Componente para exibição otimizada de imagens --}}

<picture>
    {{-- AVIF (mais moderno e eficiente) --}}
    <source 
        type="image/avif"
        srcset="{{ $image->getUrl('thumb.avif') }} 300w,
                {{ $image->getUrl('preview.avif') }} 600w,
                {{ $image->getUrl('large.avif') }} 1200w"
        sizes="(max-width: 640px) 100vw, 
               (max-width: 1024px) 50vw, 
               33vw">
    
    {{-- WebP (boa compatibilidade) --}}
    <source 
        type="image/webp"
        srcset="{{ $image->getUrl('thumb.webp') }} 300w,
                {{ $image->getUrl('preview.webp') }} 600w,
                {{ $image->getUrl('large.webp') }} 1200w"
        sizes="(max-width: 640px) 100vw, 
               (max-width: 1024px) 50vw, 
               33vw">
    
    {{-- JPEG fallback --}}
    <img 
        src="{{ $image->getUrl('preview') }}"
        srcset="{{ $image->getUrl('thumb') }} 300w,
                {{ $image->getUrl('preview') }} 600w,
                {{ $image->getUrl('large') }} 1200w"
        sizes="(max-width: 640px) 100vw, 
               (max-width: 1024px) 50vw, 
               33vw"
        alt="{{ $alt }}"
        loading="lazy"
        decoding="async"
        style="background-color: {{ $image->dominant_color }};"
        class="w-full h-full object-cover {{ $class ?? '' }}"
        @if($image->blurhash)
        data-blurhash="{{ $image->blurhash }}"
        @endif>
</picture>
📦 Pacotes Recomendados
json{
  "require": {
    "spatie/laravel-medialibrary": "^11.0",
    "intervention/image": "^3.0",
    "spatie/laravel-image-optimizer": "^1.7",
    "kornrunner/blurhash": "^1.2"
  },
  "require-dev": {
    "spatie/laravel-ray": "^1.33"
  }
}
✅ Checklist de Implementação
Fase 1: Setup Base

 Instalar Spatie Media Library
 Configurar conversões de imagem
 Implementar upload com FilePond
 Criar API de upload

Fase 2: Otimização

 Implementar queue de otimização
 Gerar formatos WebP/AVIF
 Implementar BlurHash
 Cache de conversões

Fase 3: Interface

 Componente de upload com preview
 Editor de imagem inline
 Drag and drop
 Ordenação de imagens

Fase 4: Performance

 Lazy loading
 CDN integration
 Responsive images
 Cache headers

Esta solução oferece upload interativo com preview, edição inline, otimização automática e melhor performance, mantendo compatibilidade com a estrutura existente do Laravel 12.Tentar novamenteO Claude pode cometer erros. Confira sempre as respostas.Pesquisa Opus 4.1