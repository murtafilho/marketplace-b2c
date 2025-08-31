<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\LocalImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductImageController extends Controller
{
    protected $imageService;
    
    public function __construct(LocalImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    
    /**
     * Upload de imagem para produto
     */
    public function upload(Request $request, Product $product): JsonResponse
    {
        try {
            // Validação básica
            $validator = Validator::make($request->all(), [
                'image' => 'required|file|mimes:jpeg,png,webp|max:5120', // 5MB
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo inválido',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $file = $request->file('image');
            
            // Validação avançada com LocalImageService
            $validation = $this->imageService->validateImage($file);
            
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagem não atende aos requisitos',
                    'errors' => $validation['errors']
                ], 422);
            }
            
            // Verificar espaço em disco
            if (!$this->imageService->checkDiskSpace($file->getSize())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Espaço insuficiente no servidor'
                ], 507);
            }
            
            // Otimizar imagem para storage local
            $optimizedFile = $this->imageService->optimizeForLocal($file);
            
            // Gerar nome único
            $fileName = $this->imageService->generateUniqueFileName($optimizedFile, 'product');
            
            // Adicionar à galeria do produto usando Spatie Media Library
            $media = $product->addMediaFromRequest('image')
                ->usingFileName($fileName)
                ->toMediaCollection('gallery', 'public');
            
            // Limpar arquivo temporário se foi criado
            if ($optimizedFile->getPathname() !== $file->getPathname()) {
                @unlink($optimizedFile->getPathname());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem enviada com sucesso',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $this->imageService->formatFileSize($media->size),
                    'mime_type' => $media->mime_type,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'medium_url' => $media->getUrl('medium')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro no upload de imagem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Listar imagens de um produto
     */
    public function index(Product $product): JsonResponse
    {
        try {
            $images = $product->getMedia('gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'size' => $this->imageService->formatFileSize($media->size),
                    'mime_type' => $media->mime_type,
                    'url' => $media->getUrl(),
                    'thumb_url' => $media->getUrl('thumb'),
                    'medium_url' => $media->getUrl('medium'),
                    'created_at' => $media->created_at->format('d/m/Y H:i')
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $images,
                'count' => $images->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao listar imagens: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar imagens'
            ], 500);
        }
    }
    
    /**
     * Remover imagem
     */
    public function destroy(Product $product, Media $media): JsonResponse
    {
        try {
            // Verificar se a mídia pertence ao produto
            if ($media->model_id !== $product->id || $media->model_type !== Product::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagem não encontrada para este produto'
                ], 404);
            }
            
            $media->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem removida com sucesso'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao remover imagem: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover imagem'
            ], 500);
        }
    }
    
    /**
     * Reordenar imagens
     */
    public function reorder(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'order' => 'required|array',
                'order.*' => 'required|integer|exists:media,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $order = $request->input('order');
            
            DB::transaction(function () use ($product, $order) {
                foreach ($order as $index => $mediaId) {
                    $media = $product->getMedia('gallery')->where('id', $mediaId)->first();
                    if ($media) {
                        $media->order_column = $index + 1;
                        $media->save();
                    }
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Ordem das imagens atualizada'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao reordenar imagens: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reordenar imagens'
            ], 500);
        }
    }
    
    /**
     * Definir imagem principal
     */
    public function setPrimary(Request $request, Product $product, Media $media): JsonResponse
    {
        try {
            // Verificar se a mídia pertence ao produto
            if ($media->model_id !== $product->id || $media->model_type !== Product::class) {
                return response()->json([
                    'success' => false,
                    'message' => 'Imagem não encontrada para este produto'
                ], 404);
            }
            
            DB::transaction(function () use ($product, $media) {
                // Remover flag primary de todas as imagens
                $product->getMedia('gallery')->each(function ($item) {
                    $item->setCustomProperty('is_primary', false);
                    $item->save();
                });
                
                // Definir como primary
                $media->setCustomProperty('is_primary', true);
                $media->save();
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Imagem principal definida'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao definir imagem principal: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao definir imagem principal'
            ], 500);
        }
    }
    
    /**
     * Upload múltiplo de imagens
     */
    public function uploadMultiple(Request $request, Product $product): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'images' => 'required|array|max:10',
                'images.*' => 'required|file|mimes:jpeg,png,webp|max:5120'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivos inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $files = $request->file('images');
            $results = [];
            $errors = [];
            
            foreach ($files as $index => $file) {
                try {
                    // Validação individual
                    $validation = $this->imageService->validateImage($file);
                    
                    if (!$validation['valid']) {
                        $errors[] = "Arquivo {$index}: " . implode(', ', $validation['errors']);
                        continue;
                    }
                    
                    // Verificar espaço
                    if (!$this->imageService->checkDiskSpace($file->getSize())) {
                        $errors[] = "Arquivo {$index}: Espaço insuficiente";
                        continue;
                    }
                    
                    // Otimizar e salvar
                    $optimizedFile = $this->imageService->optimizeForLocal($file);
                    $fileName = $this->imageService->generateUniqueFileName($optimizedFile, 'product');
                    
                    $media = $product->addMedia($optimizedFile)
                        ->usingFileName($fileName)
                        ->toMediaCollection('gallery', 'public');
                    
                    $results[] = [
                        'id' => $media->id,
                        'name' => $media->name,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb')
                    ];
                    
                    // Limpar arquivo temporário
                    if ($optimizedFile->getPathname() !== $file->getPathname()) {
                        @unlink($optimizedFile->getPathname());
                    }
                    
                } catch (\Exception $e) {
                    $errors[] = "Arquivo {$index}: " . $e->getMessage();
                }
            }
            
            return response()->json([
                'success' => count($results) > 0,
                'message' => count($results) . ' imagens enviadas com sucesso',
                'data' => $results,
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro no upload múltiplo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro no upload múltiplo'
            ], 500);
        }
    }
    
    /**
     * Obter estatísticas de storage
     */
    public function getStorageStats(): JsonResponse
    {
        try {
            $stats = $this->imageService->getStorageStats();
            
            // Adicionar contagem de imagens
            $imageCount = Media::where('collection_name', 'gallery')
                ->where('model_type', Product::class)
                ->count();
            
            $stats['total_images'] = $imageCount;
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao obter estatísticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas'
            ], 500);
        }
    }
    
    /**
     * Limpar imagens não utilizadas
     */
    public function cleanup(): JsonResponse
    {
        try {
            // Limpar arquivos temporários
            $tempCleaned = $this->imageService->cleanupTempFiles();
            
            // Limpar imagens órfãs
            $this->imageService->cleanupUnusedImages();
            
            return response()->json([
                'success' => true,
                'message' => "Limpeza concluída. {$tempCleaned} arquivos temporários removidos."
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro na limpeza: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro na limpeza'
            ], 500);
        }
    }
}