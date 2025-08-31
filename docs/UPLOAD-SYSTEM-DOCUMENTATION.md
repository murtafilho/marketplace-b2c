# Guia de Upload de Imagens para Marketplace Laravel - Armazenamento Local

## 1. Configuração de Armazenamento Local

### Estrutura de Diretórios Local
```bash
# Estrutura recomendada para armazenamento local
storage/app/public/
├── products/           # Imagens de produtos
│   ├── thumbnails/    # Miniaturas (150x150)
│   ├── medium/        # Médias (500x500)
│   └── large/         # Grandes (1200x1200)
├── stores/            # Logos e banners de lojas
│   ├── logos/
│   └── banners/
├── users/             # Avatares de usuários
│   └── avatars/
└── temp/              # Uploads temporários (limpar periodicamente)
```

### Configuração Inicial do Storage Local
```bash
# Arquivo: Terminal
# Criar link simbólico para acesso público
php artisan storage:link

# Criar estrutura de pastas
mkdir -p storage/app/public/{products,stores,users,temp}
mkdir -p storage/app/public/products/{thumbnails,medium,large}
mkdir -p storage/app/public/stores/{logos,banners}
mkdir -p storage/app/public/users/avatars

# Definir permissões corretas
chmod -R 755 storage/app/public
```

### Configuração do Filesystem
```php
// Arquivo: config/filesystems.php
<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),
    
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Disco específico para produtos (opcional, mas recomendado)
        'products' => [
            'driver' => 'local',
            'root' => storage_path('app/public/products'),
            'url' => env('APP_URL').'/storage/products',
            'visibility' => 'public',
        ],

        // Disco para uploads temporários
        'temp' => [
            'driver' => 'local',
            'root' => storage_path('app/public/temp'),
            'url' => env('APP_URL').'/storage/temp',
            'visibility' => 'public',
        ],
    ],
];
```

## 2. Configuração do Spatie Media Library para Storage Local

### Publicar e Configurar
```bash
# Arquivo: Terminal
# Publicar configurações
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"

# Rodar migrations
php artisan migrate
```

### Arquivo de Configuração para Storage Local
```php
// Arquivo: config/medialibrary.php
<?php

return [
    /*
     * Disco onde os arquivos serão armazenados (local)
     */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * Tamanho máximo do arquivo em bytes (5MB)
     */
    'max_file_size' => 1024 * 1024 * 5,

    /*
     * Queue para conversões (desabilitado para desenvolvimento local)
     */
    'queue_conversions_by_default' => env('QUEUE_CONVERSIONS', false),
    
    /*
     * Path onde arquivos temporários serão armazenados
     */
    'temporary_directory_path' => storage_path('app/public/temp'),

    /*
     * Prefixo da URL para servir mídia local
     */
    'prefix' => '/storage',

    /*
     * Model da mídia
     */
    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
     * Diretório para conversões temporárias
     */
    'conversions_disk_name' => env('MEDIA_DISK', 'public'),

    /*
     * Caminho para imagem fallback
     */
    'fallback_url' => '/images/placeholder.jpg',
    'fallback_path' => public_path('/images/placeholder.jpg'),

    /*
     * Driver de manipulação de imagem (GD é padrão no PHP)
     */
    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    /*
     * Otimizadores de imagem (instalar via apt-get ou brew se disponível)
     */
    'image_optimizers' => [
        Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
            '-m85', // qualidade máxima 85
            '--strip-all', // remove metadados
            '--all-progressive',
        ],
        Spatie\ImageOptimizer\Optimizers\Pngquant::class => [
            '--force',
            '--skip-if-larger',
        ],
        Spatie\ImageOptimizer\Optimizers\Optipng::class => [
            '-i0',
            '-o2',
            '-quiet',
        ],
    ],

    /*
     * Gerar responsivas (desabilitado para economizar espaço local)
     */
    'generate_responsive_images' => env('RESPONSIVE_IMAGES', false),

    /*
     * Jobs
     */
    'jobs' => [
        'perform_conversions' => \Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob::class,
        'generate_responsive_images' => \Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob::class,
    ],

    /*
     * Quando usar storage local, não precisa de headers remotos
     */
    'remote' => [
        'extra_headers' => [],
    ],
];
```

## 3. Model Configuration com Storage Local

```php
// Arquivo: app/Models/Product.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'price',
        'store_id',
        'category_id',
        'status'
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail para listagens (processo rápido, não usa queue)
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->quality(70)
            ->optimize()
            ->performOnCollections('products', 'gallery')
            ->nonQueued(); // Importante: processa imediatamente no storage local

        // Imagem média para cards
        $this->addMediaConversion('medium')
            ->width(500)
            ->height(500)
            ->quality(80)
            ->optimize()
            ->performOnCollections('products', 'gallery')
            ->nonQueued(); // Para desenvolvimento local

        // Imagem grande para zoom/detalhes
        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->quality(90)
            ->optimize()
            ->performOnCollections('products', 'gallery')
            ->nonQueued();

        // WebP para performance (opcional no local)
        if (extension_loaded('imagick') || extension_loaded('gd')) {
            $this->addMediaConversion('webp')
                ->width(800)
                ->height(800)
                ->format('webp')
                ->quality(85)
                ->performOnCollections('products', 'gallery')
                ->nonQueued();
        }
    }

    public function registerMediaCollections(): void
    {
        // Imagem principal do produto
        $this->addMediaCollection('products')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile()
            ->useDisk('public') // Usar disco público local
            ->useFallbackUrl('/images/product-placeholder.jpg');

        // Galeria de imagens
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public'); // Usar disco público local
    }
}
```

## 4. Controller Otimizado para Storage Local

```php
// Arquivo: app/Http/Controllers/ProductImageController.php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\LocalImageService;

class ProductImageController extends Controller
{
    protected $imageService;

    public function __construct(LocalImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Upload de imagem principal do produto (storage local)
     */
    public function uploadMainImage(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,webp',
                'max:5120', // 5MB
                'dimensions:min_width=300,min_height=300,max_width=4000,max_height=4000'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Limpar cache de imagens antigas antes de remover
            $this->clearOldImageCache($product, 'products');

            // Remove imagem anterior se existir
            $product->clearMediaCollection('products');

            // Gerar nome único para o arquivo
            $fileName = $this->generateFileName($request->file('image'), $product->id);

            // Adiciona nova imagem com processamento local
            $media = $product->addMediaFromRequest('image')
                ->usingName($product->name)
                ->usingFileName($fileName)
                ->sanitizingFileName(function($fileName) {
                    return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                })
                ->toMediaCollection('products');

            // Limpar arquivos temporários
            $this->clearTempFiles();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'medium' => $media->getUrl('medium'),
                    'large' => $media->getUrl('large'),
                    'size' => $this->formatFileSize($media->size),
                    'mime_type' => $media->mime_type
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log do erro para debug local
            \Log::error('Erro no upload de imagem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload da imagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload múltiplo para galeria (storage local)
     */
    public function uploadGallery(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:10',
            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,png,webp',
                'max:5120',
                'dimensions:min_width=300,min_height=300,max_width=4000,max_height=4000'
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar espaço em disco local
        if (!$this->checkDiskSpace($request->file('images'))) {
            return response()->json([
                'success' => false,
                'message' => 'Espaço em disco insuficiente'
            ], 507);
        }

        try {
            DB::beginTransaction();

            $uploadedImages = [];
            $currentGalleryCount = $product->getMedia('gallery')->count();
            $maxGalleryImages = 10;

            if ($currentGalleryCount >= $maxGalleryImages) {
                return response()->json([
                    'success' => false,
                    'message' => "Limite máximo de {$maxGalleryImages} imagens na galeria"
                ], 422);
            }

            foreach ($request->file('images') as $index => $image) {
                if ($currentGalleryCount + $index >= $maxGalleryImages) {
                    break;
                }

                $fileName = $this->generateFileName($image, $product->id . '-gallery-' . ($index + 1));

                $media = $product->addMedia($image)
                    ->usingName($product->name . ' - Galeria ' . ($index + 1))
                    ->usingFileName($fileName)
                    ->sanitizingFileName(function($fileName) {
                        return strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                    })
                    ->toMediaCollection('gallery');

                $uploadedImages[] = [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'medium' => $media->getUrl('medium'),
                    'size' => $this->formatFileSize($media->size),
                    'position' => $currentGalleryCount + $index
                ];
            }

            // Limpar arquivos temporários
            $this->clearTempFiles();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $uploadedImages,
                'message' => count($uploadedImages) . ' imagens adicionadas com sucesso'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erro no upload de galeria: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload das imagens'
            ], 500);
        }
    }

    /**
     * Deletar imagem (storage local)
     */
    public function deleteImage(Product $product, $mediaId)
    {
        try {
            $media = $product->media()->findOrFail($mediaId);
            
            // Guardar paths antes de deletar
            $paths = [
                $media->getPath(),
                $media->getPath('thumb'),
                $media->getPath('medium'),
                $media->getPath('large')
            ];

            // Deletar registro do banco
            $media->delete();

            // Garantir que arquivos físicos sejam removidos
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagem removida com sucesso'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao deletar imagem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover imagem'
            ], 500);
        }
    }

    /**
     * Obter todas as imagens do produto
     */
    public function getImages(Product $product)
    {
        $mainImage = $product->getFirstMedia('products');
        $galleryImages = $product->getMedia('gallery');

        return response()->json([
            'success' => true,
            'data' => [
                'main' => $mainImage ? [
                    'id' => $mainImage->id,
                    'url' => $mainImage->getUrl(),
                    'thumb' => $mainImage->getUrl('thumb'),
                    'medium' => $mainImage->getUrl('medium'),
                    'large' => $mainImage->getUrl('large'),
                    'size' => $this->formatFileSize($mainImage->size)
                ] : null,
                'gallery' => $galleryImages->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb' => $media->getUrl('thumb'),
                        'medium' => $media->getUrl('medium'),
                        'size' => $this->formatFileSize($media->size)
                    ];
                })
            ]
        ]);
    }

    /**
     * Gerar nome único para arquivo
     */
    private function generateFileName($file, $prefix = '')
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = $prefix . '-' . Str::random(20) . '-' . time();
        
        return $fileName . '.' . $extension;
    }

    /**
     * Verificar espaço em disco
     */
    private function checkDiskSpace($files)
    {
        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        $freeSpace = disk_free_space(storage_path('app/public'));
        $requiredSpace = $totalSize * 3; // Considera conversões

        return $freeSpace > $requiredSpace;
    }

    /**
     * Limpar arquivos temporários antigos
     */
    private function clearTempFiles()
    {
        $tempPath = storage_path('app/public/temp');
        $files = glob($tempPath . '/*');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                // Remove arquivos com mais de 1 hora
                if ($now - filemtime($file) >= 3600) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Limpar cache de imagens antigas
     */
    private function clearOldImageCache($product, $collection)
    {
        $oldMedia = $product->getMedia($collection);
        foreach ($oldMedia as $media) {
            // Limpar conversões do cache se existirem
            $conversions = ['thumb', 'medium', 'large', 'webp'];
            foreach ($conversions as $conversion) {
                $path = $media->getPath($conversion);
                if (file_exists($path)) {
                    // Cache será regenerado quando necessário
                }
            }
        }
    }

    /**
     * Formatar tamanho do arquivo
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
```

## 5. Service para Gerenciamento Local de Imagens

```php
// Arquivo: app/Services/LocalImageService.php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalImageService
{
    /**
     * Otimizar imagem antes de salvar localmente
     */
    public function optimizeForLocal(UploadedFile $file): UploadedFile
    {
        $image = Image::make($file);
        
        // Obter orientação correta da imagem
        $image->orientate();
        
        // Redimensionar se muito grande para economizar espaço local
        if ($image->width() > 2000 || $image->height() > 2000) {
            $image->resize(2000, 2000, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Remover metadados EXIF para economizar espaço
        if (method_exists($image, 'stripImage')) {
            $image->stripImage();
        }
        
        // Salvar temporariamente com compressão otimizada
        $tempPath = storage_path('app/public/temp/' . Str::random(40) . '.' . $file->getClientOriginalExtension());
        
        // Garantir que o diretório temp existe
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }
        
        // Salvar com qualidade otimizada para storage local
        $image->save($tempPath, 85);
        
        return new UploadedFile(
            $tempPath,
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            null,
            true
        );
    }
    
    /**
     * Limpar storage local de imagens não utilizadas
     */
    public function cleanupUnusedImages()
    {
        $mediaPath = storage_path('app/public/products');
        $dbImages = \DB::table('media')
            ->where('disk', 'public')
            ->pluck('id', 'file_name')
            ->toArray();
        
        $files = Storage::disk('public')->files('products');
        
        foreach ($files as $file) {
            $fileName = basename($file);
            if (!isset($dbImages[$fileName])) {
                Storage::disk('public')->delete($file);
            }
        }
    }
    
    /**
     * Obter estatísticas de uso do storage local
     */
    public function getStorageStats()
    {
        $path = storage_path('app/public');
        
        return [
            'total_space' => disk_total_space($path),
            'free_space' => disk_free_space($path),
            'used_space' => disk_total_space($path) - disk_free_space($path),
            'percentage_used' => round((1 - disk_free_space($path) / disk_total_space($path)) * 100, 2),
            'products_size' => $this->getDirectorySize(storage_path('app/public/products')),
            'temp_size' => $this->getDirectorySize(storage_path('app/public/temp'))
        ];
    }
    
    /**
     * Calcular tamanho de diretório
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        if (!file_exists($path)) {
            return $size;
        }
        
        foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->getDirectorySize($each);
        }
        
        return $size;
    }
}
```

## 6. Component Alpine.js Otimizado para Upload Local

```html
<!-- Arquivo: resources/views/components/image-upload-local.blade.php -->
<div x-data="localImageUploader()" class="w-full">
    <!-- Upload de Imagem Principal -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Imagem Principal do Produto
        </label>
        
        <div class="relative">
            <!-- Preview da Imagem Principal -->
            <div x-show="mainImage" class="relative group">
                <img :src="mainImageUrl" 
                     alt="Imagem principal do produto"
                     class="w-full h-64 object-cover rounded-lg shadow-md">
                
                <!-- Loading overlay durante processamento -->
                <div x-show="processingMain" 
                     class="absolute inset-0 bg-white bg-opacity-75 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Processando imagem...</p>
                    </div>
                </div>
                
                <!-- Botões de ação -->
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 
                            group-hover:opacity-100 transition-opacity rounded-lg 
                            flex items-center justify-center"
                     x-show="!processingMain">
                    <button @click="removeMainImage()" 
                            type="button"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg 
                                   hover:bg-red-600 transition flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Remover
                    </button>
                </div>
            </div>

            <!-- Área de Upload -->
            <div x-show="!mainImage" 
                 @click="$refs.mainImageInput.click()"
                 @dragover.prevent="dragover = true"
                 @dragleave.prevent="dragover = false"
                 @drop.prevent="handleMainImageDrop($event)"
                 :class="{'border-blue-500 bg-blue-50': dragover}"
                 class="border-2 border-dashed border-gray-300 rounded-lg p-8 
                        text-center cursor-pointer hover:border-gray-400 
                        transition-all duration-200">
                
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" 
                     fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" 
                          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                
                <p class="mt-2 text-sm text-gray-600">
                    <span class="font-semibold">Clique para enviar</span> ou arraste uma imagem aqui
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    PNG, JPG, WEBP até 5MB (mínimo 300x300px)
                </p>
            </div>

            <input type="file" 
                   x-ref="mainImageInput"
                   @change="handleMainImageSelect($event)"
                   accept="image/jpeg,image/png,image/webp"
                   class="hidden">
        </div>

        <!-- Barra de Progresso -->
        <div x-show="uploadingMain" x-transition class="mt-2">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Enviando...</span>
                <span x-text="`${uploadProgress}%`"></span>
            </div>
            <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${uploadProgress}%`"></div>
            </div>
        </div>

        <!-- Mensagens de erro -->
        <div x-show="mainImageError" x-transition class="mt-2">
            <p class="text-sm text-red-600" x-text="mainImageError"></p>
        </div>
    </div>

    <!-- Galeria de Imagens -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <label class="block text-sm font-medium text-gray-700">
                Galeria de Imagens
            </label>
            <span class="text-xs text-gray-500">
                <span x-text="galleryImages.length"></span>/10 imagens
            </span>
        </div>

        <!-- Grid de Imagens -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-4">
            <template x-for="(image, index) in galleryImages" :key="image.id">
                <div class="relative group" 
                     draggable="true"
                     @dragstart="dragStart(index)"
                     @dragover.prevent="dragOver(index)"
                     @drop.prevent="drop(index)"
                     :class="{'ring-2 ring-blue-500': draggingIndex === index}">
                    
                    <img :src="image.thumb || image.url" 
                         :alt="`Imagem ${index + 1} da galeria`"
                         class="w-full h-32 object-cover rounded-lg shadow-md">
                    
                    <!-- Loading overlay -->
                    <div x-show="image.processing" 
                         class="absolute inset-0 bg-white bg-opacity-75 rounded-lg flex items-center justify-center">
                        <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    
                    <!-- Overlay com Ações -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 
                                group-hover:opacity-100 transition-opacity rounded-lg 
                                flex items-center justify-center space-x-2"
                         x-show="!image.processing">
                        
                        <!-- Botão de Visualizar -->
                        <button @click="viewImage(image)" 
                                type="button"
                                title="Visualizar"
                                class="bg-white text-gray-700 p-2 rounded-lg 
                                       hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>

                        <!-- Botão de Remover -->
                        <button @click="removeGalleryImage(index)" 
                                type="button"
                                title="Remover"
                                class="bg-red-500 text-white p-2 rounded-lg 
                                       hover:bg-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Indicador de Posição -->
                    <div class="absolute top-2 left-2 bg-black bg-opacity-50 
                                text-white text-xs px-2 py-1 rounded">
                        #<span x-text="index + 1"></span>
                    </div>

                    <!-- Tamanho do arquivo -->
                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 
                                text-white text-xs px-2 py-1 rounded"
                         x-show="image.size">
                        <span x-text="image.size"></span>
                    </div>
                </div>
            </template>

            <!-- Botão Adicionar -->
            <div x-show="galleryImages.length < 10"
                 @click="$refs.galleryInput.click()"
                 @dragover.prevent="galleryDragover = true"
                 @dragleave.prevent="galleryDragover = false"
                 @drop.prevent="handleGalleryDrop($event)"
                 :class="{'border-blue-500 bg-blue-50': galleryDragover}"
                 class="border-2 border-dashed border-gray-300 rounded-lg 
                        h-32 flex flex-col items-center justify-center cursor-pointer 
                        hover:border-gray-400 transition-colors">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="text-xs text-gray-500 mt-1">Adicionar</span>
            </div>
        </div>

        <input type="file" 
               x-ref="galleryInput"
               @change="handleGallerySelect($event)"
               accept="image/jpeg,image/png,image/webp"
               multiple
               class="hidden">

        <!-- Barra de Progresso da Galeria -->
        <div x-show="uploadingGallery" x-transition class="mt-2">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Enviando <span x-text="galleryUploadCount"></span> imagens...</span>
                <span x-text="`${galleryProgress}%`"></span>
            </div>
            <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                     :style="`width: ${galleryProgress}%`"></div>
            </div>
        </div>

        <!-- Mensagens de erro da galeria -->
        <div x-show="galleryError" x-transition class="mt-2">
            <p class="text-sm text-red-600" x-text="galleryError"></p>
        </div>
    </div>

    <!-- Modal de Visualização -->
    <div x-show="viewingImage" 
         x-transition
         @click="viewingImage = null"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75">
        <div class="relative max-w-4xl max-h-full">
            <img :src="viewingImage?.url || viewingImage?.medium" 
                 alt="Visualização ampliada"
                 class="max-w-full max-h-full rounded-lg shadow-2xl"
                 @click.stop>
            
            <button @click="viewingImage = null" 
                    type="button"
                    class="absolute top-4 right-4 text-white bg-black bg-opacity-50 
                           rounded-full p-2 hover:bg-opacity-75 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Informações da imagem -->
            <div class="absolute bottom-4 left-4 bg-black bg-opacity-75 text-white p-3 rounded-lg"
                 x-show="viewingImage?.size"
                 @click.stop>
                <p class="text-sm">Tamanho: <span x-text="viewingImage?.size"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
function localImageUploader() {
    return {
        mainImage: null,
        mainImageUrl: null,
        mainImageError: null,
        galleryImages: [],
        galleryError: null,
        dragover: false,
        galleryDragover: false,
        uploadingMain: false,
        uploadingGallery: false,
        processingMain: false,
        uploadProgress: 0,
        galleryProgress: 0,
        galleryUploadCount: 0,
        viewingImage: null,
        draggedIndex: null,
        draggingIndex: null,
        productId: @json($product->id ?? null),

        init() {
            // Carregar imagens existentes se estiver editando
            if (this.productId) {
                this.loadExistingImages();
            }
        },

        async loadExistingImages() {
            try {
                const response = await fetch(`/api/products/${this.productId}/images`);
                const data = await response.json();
                
                if (data.success) {
                    if (data.data.main) {
                        this.mainImage = data.data.main;
                        this.mainImageUrl = data.data.main.url;
                    }
                    
                    if (data.data.gallery) {
                        this.galleryImages = data.data.gallery;
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar imagens:', error);
            }
        },

        validateImage(file) {
            // Reset errors
            this.mainImageError = null;
            this.galleryError = null;

            // Validar tipo
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                return 'Formato de arquivo inválido. Use JPG, PNG ou WEBP.';
            }

            // Validar tamanho (5MB)
            if (file.size > 5 * 1024 * 1024) {
                return 'A imagem deve ter no máximo 5MB.';
            }

            return null;
        },

        handleMainImageSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const error = this.validateImage(file);
                if (error) {
                    this.mainImageError = error;
                    return;
                }
                this.uploadMainImage(file);
            }
        },

        handleMainImageDrop(event) {
            this.dragover = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const error = this.validateImage(file);
                if (error) {
                    this.mainImageError = error;
                    return;
                }
                this.uploadMainImage(file);
            }
        },

        async uploadMainImage(file) {
            // Preview local imediato
            const reader = new FileReader();
            reader.onload = (e) => {
                this.mainImageUrl = e.target.result;
                this.processingMain = true;
            };
            reader.readAsDataURL(file);

            // Upload para servidor
            const formData = new FormData();
            formData.append('image', file);

            this.uploadingMain = true;
            this.uploadProgress = 0;
            this.mainImageError = null;

            // Simular progresso
            const progressInterval = setInterval(() => {
                if (this.uploadProgress < 90) {
                    this.uploadProgress += 10;
                }
            }, 100);

            try {
                const response = await fetch(`/api/products/${this.productId}/main-image`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                clearInterval(progressInterval);
                this.uploadProgress = 100;

                const data = await response.json();

                if (data.success) {
                    this.mainImage = data.data;
                    this.mainImageUrl = data.data.url;
                    this.showNotification('Imagem principal carregada com sucesso', 'success');
                } else {
                    this.mainImageError = data.message || 'Erro ao fazer upload da imagem';
                    this.mainImage = null;
                    this.mainImageUrl = null;
                }
            } catch (error) {
                clearInterval(progressInterval);
                console.error('Erro:', error);
                this.mainImageError = 'Erro de conexão ao fazer upload';
                this.mainImage = null;
                this.mainImageUrl = null;
            } finally {
                this.uploadingMain = false;
                this.processingMain = false;
                this.uploadProgress = 0;
            }
        },

        async removeMainImage() {
            if (!confirm('Deseja remover a imagem principal?')) {
                return;
            }

            if (this.mainImage?.id) {
                try {
                    const response = await fetch(`/api/products/${this.productId}/images/${this.mainImage.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.showNotification('Imagem removida com sucesso', 'success');
                    }
                } catch (error) {
                    console.error('Erro ao remover imagem:', error);
                }
            }

            this.mainImage = null;
            this.mainImageUrl = null;
        },

        handleGallerySelect(event) {
            const files = Array.from(event.target.files);
            this.uploadGalleryImages(files);
        },

        handleGalleryDrop(event) {
            this.galleryDragover = false;
            const files = Array.from(event.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            this.uploadGalleryImages(files);
        },

        async uploadGalleryImages(files) {
            const remainingSlots = 10 - this.galleryImages.length;
            if (remainingSlots <= 0) {
                this.galleryError = 'Limite máximo de 10 imagens atingido';
                return;
            }

            const filesToUpload = files.slice(0, remainingSlots);
            
            // Validar todos os arquivos
            for (let file of filesToUpload) {
                const error = this.validateImage(file);
                if (error) {
                    this.galleryError = error;
                    return;
                }
            }

            const formData = new FormData();
            filesToUpload.forEach(file => {
                formData.append('images[]', file);
            });

            this.uploadingGallery = true;
            this.galleryProgress = 0;
            this.galleryUploadCount = filesToUpload.length;
            this.galleryError = null;

            // Simular progresso
            const progressInterval = setInterval(() => {
                if (this.galleryProgress < 90) {
                    this.galleryProgress += 10;
                }
            }, 200);

            try {
                const response = await fetch(`/api/products/${this.productId}/gallery`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                clearInterval(progressInterval);
                this.galleryProgress = 100;

                const data = await response.json();

                if (data.success) {
                    this.galleryImages.push(...data.data);
                    this.showNotification(data.message || 'Imagens adicionadas à galeria', 'success');
                } else {
                    this.galleryError = data.message || 'Erro ao fazer upload das imagens';
                }
            } catch (error) {
                clearInterval(progressInterval);
                console.error('Erro:', error);
                this.galleryError = 'Erro de conexão ao fazer upload';
            } finally {
                this.uploadingGallery = false;
                this.galleryProgress = 0;
                this.galleryUploadCount = 0;
            }
        },

        async removeGalleryImage(index) {
            if (!confirm('Deseja remover esta imagem da galeria?')) {
                return;
            }

            const image = this.galleryImages[index];
            
            if (image?.id) {
                try {
                    const response = await fetch(`/api/products/${this.productId}/images/${image.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        this.galleryImages.splice(index, 1);
                        this.showNotification('Imagem removida da galeria', 'success');
                    }
                } catch (error) {
                    console.error('Erro ao remover imagem:', error);
                }
            } else {
                this.galleryImages.splice(index, 1);
            }
        },

        viewImage(image) {
            this.viewingImage = image;
        },

        // Drag and Drop para reordenar
        dragStart(index) {
            this.draggedIndex = index;
            this.draggingIndex = index;
        },

        dragOver(index) {
            if (this.draggedIndex !== null && this.draggedIndex !== index) {
                const draggedImage = this.galleryImages[this.draggedIndex];
                this.galleryImages.splice(this.draggedIndex, 1);
                this.galleryImages.splice(index, 0, draggedImage);
                this.draggedIndex = index;
            }
        },

        drop(index) {
            this.draggedIndex = null;
            this.draggingIndex = null;
            this.saveGalleryOrder();
        },

        async saveGalleryOrder() {
            const order = this.galleryImages.map(img => img.id);
            
            try {
                const response = await fetch(`/api/products/${this.productId}/reorder-gallery`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order })
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Ordem das imagens atualizada', 'success');
                }
            } catch (error) {
                console.error('Erro ao salvar ordem:', error);
            }
        },

        showNotification(message, type = 'info') {
            // Implementar sistema de notificação
            // Por enquanto, apenas console.log
            console.log(`[${type.toUpperCase()}]:`, message);
            
            // Se você tiver um sistema de toast/notificação, use aqui
            // Por exemplo, com Alpine Toast:
            // this.$dispatch('notify', { message, type });
        }
    }
}
</script>
```

## 7. Comando Artisan para Limpeza do Storage Local

```php
// Arquivo: app/Console/Commands/CleanLocalStorage.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\LocalImageService;

class CleanLocalStorage extends Command
{
    protected $signature = 'storage:clean-local {--force : Forçar limpeza sem confirmação}';
    protected $description = 'Limpar arquivos temporários e não utilizados do storage local';

    protected $imageService;

    public function __construct(LocalImageService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    public function handle()
    {
        $this->info('Analisando storage local...');
        
        // Obter estatísticas
        $stats = $this->imageService->getStorageStats();
        
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Espaço Total', $this->formatBytes($stats['total_space'])],
                ['Espaço Usado', $this->formatBytes($stats['used_space'])],
                ['Espaço Livre', $this->formatBytes($stats['free_space'])],
                ['Porcentagem Usada', $stats['percentage_used'] . '%'],
                ['Tamanho Produtos', $this->formatBytes($stats['products_size'])],
                ['Tamanho Temp', $this->formatBytes($stats['temp_size'])],
            ]
        );

        if (!$this->option('force')) {
            if (!$this->confirm('Deseja limpar arquivos temporários e não utilizados?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        // Limpar arquivos temporários
        $this->info('Limpando arquivos temporários...');
        $tempPath = storage_path('app/public/temp');
        $tempFiles = glob($tempPath . '/*');
        $deletedTemp = 0;
        
        foreach ($tempFiles as $file) {
            if (is_file($file) && (time() - filemtime($file) >= 3600)) {
                unlink($file);
                $deletedTemp++;
            }
        }
        
        $this->info("$deletedTemp arquivos temporários removidos.");

        // Limpar imagens órfãs
        $this->info('Procurando imagens órfãs...');
        $this->imageService->cleanupUnusedImages();
        
        // Estatísticas após limpeza
        $newStats = $this->imageService->getStorageStats();
        $freedSpace = $stats['used_space'] - $newStats['used_space'];
        
        $this->info('Limpeza concluída!');
        $this->info('Espaço liberado: ' . $this->formatBytes($freedSpace));
        
        return 0;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
```

## 8. Configuração do Cron para Limpeza Automática

```php
// Arquivo: app/Console/Kernel.php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Limpar storage local diariamente às 3AM
        $schedule->command('storage:clean-local --force')
                 ->daily()
                 ->at('03:00')
                 ->appendOutputTo(storage_path('logs/storage-cleanup.log'));
        
        // Limpar uploads temporários a cada hora
        $schedule->call(function () {
            $tempPath = storage_path('app/public/temp');
            $files = glob($tempPath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && (time() - filemtime($file) >= 3600)) {
                    unlink($file);
                }
            }
        })->hourly();
    }
}
```

## 9. Rotas Atualizadas

```php
// Arquivo: routes/web.php
<?php

use App\Http\Controllers\ProductImageController;

// Rotas para páginas
Route::middleware(['auth'])->group(function () {
    Route::get('/products/{product}/images', function ($product) {
        return view('products.images', compact('product'));
    })->name('products.images');
});

// Arquivo: routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    // Upload de imagens (storage local)
    Route::post('/products/{product}/main-image', [ProductImageController::class, 'uploadMainImage'])
        ->name('api.products.main-image');
    
    Route::post('/products/{product}/gallery', [ProductImageController::class, 'uploadGallery'])
        ->name('api.products.gallery');
    
    Route::post('/products/{product}/reorder-gallery', [ProductImageController::class, 'reorderGallery'])
        ->name('api.products.reorder-gallery');
    
    Route::delete('/products/{product}/images/{media}', [ProductImageController::class, 'deleteImage'])
        ->name('api.products.delete-image');
    
    Route::get('/products/{product}/images', [ProductImageController::class, 'getImages'])
        ->name('api.products.get-images');
});
```

## 10. Arquivo .env para Configuração Local

```env
# Arquivo: .env
# Configurações de Storage Local
FILESYSTEM_DISK=local
MEDIA_DISK=public

# Desabilitar queue para desenvolvimento local
QUEUE_CONVERSIONS=false
RESPONSIVE_IMAGES=false

# Driver de imagem (gd vem por padrão no PHP)
IMAGE_DRIVER=gd

# URL da aplicação (importante para URLs das imagens)
APP_URL=http://localhost:8000
```

## 11. Dicas de Performance para Storage Local

### Otimizações Recomendadas

1. **Limite de Upload Simultâneo**
   - Máximo 10 imagens por vez
   - Tamanho máximo 5MB por imagem

2. **Processamento de Imagens**
   - Usar GD ao invés de Imagick (mais leve)
   - Processar conversões sem queue em desenvolvimento
   - Gerar apenas 3 tamanhos essenciais

3. **Limpeza Regular**
   - Executar limpeza diária via cron
   - Remover arquivos temporários a cada hora
   - Monitorar espaço em disco

4. **Cache de Imagens**
   - Usar cache do navegador com headers apropriados
   - Implementar lazy loading no frontend

## Comandos Úteis para Gerenciamento Local

```bash
# Verificar espaço em disco
php artisan storage:clean-local

# Limpar storage forçadamente
php artisan storage:clean-local --force

# Recriar link simbólico
php artisan storage:link

# Verificar permissões
ls -la storage/app/public/

# Contar arquivos no storage
find storage/app/public -type f | wc -l

# Tamanho total do storage
du -sh storage/app/public/
```

## Migração Futura para Cloud

Quando estiver pronto para migrar para cloud (S3, DigitalOcean Spaces), você precisará apenas:

1. Alterar a configuração do disco no `.env`
2. Executar um comando de migração dos arquivos
3. Atualizar as URLs no banco de dados

O código está preparado para essa transição sem grandes modificações!