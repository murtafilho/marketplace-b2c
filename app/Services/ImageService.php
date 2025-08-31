<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Tamanho máximo do arquivo em KB
     */
    const MAX_FILE_SIZE = 5120; // 5MB

    /**
     * Processar upload de imagem (versão simplificada)
     */
    public function processProductImage(UploadedFile $file, int $productId): array
    {
        // Validar tipo de arquivo
        $this->validateImage($file);
        
        // Gerar nome único
        $fileName = $this->generateFileName($file);
        $basePath = "products/{$productId}";
        
        // Criar diretórios se não existirem
        $this->ensureDirectoryExists($basePath);
        
        // Salvar arquivo original
        $originalPath = "{$basePath}/{$fileName}";
        $thumbnailPath = "{$basePath}/thumb_{$fileName}";
        
        // Salvar imagem original
        Storage::disk('public')->put($originalPath, file_get_contents($file));
        
        // Criar thumbnail simples (cópia por enquanto)
        Storage::disk('public')->put($thumbnailPath, file_get_contents($file));
        
        // Obter metadados básicos
        $imageInfo = getimagesize($file->getPathname());
        $metadata = [
            'width' => $imageInfo[0] ?? 0,
            'height' => $imageInfo[1] ?? 0,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ];
        
        return [
            'file_path' => $originalPath,
            'thumbnail_path' => $thumbnailPath,
            'metadata' => $metadata,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    /**
     * Validar imagem
     */
    private function validateImage(UploadedFile $file): void
    {
        $mimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        
        if (!in_array($file->getMimeType(), $mimeTypes)) {
            throw new \Exception('Tipo de arquivo não suportado. Use JPEG, PNG ou WebP.');
        }
        
        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            throw new \Exception('Arquivo muito grande. Tamanho máximo: ' . self::MAX_FILE_SIZE / 1024 . 'MB');
        }
        
        // Validar se é realmente uma imagem
        $imageInfo = @getimagesize($file->getPathname());
        if (!$imageInfo) {
            throw new \Exception('Arquivo inválido ou corrompido.');
        }
        
        // Verificar dimensões mínimas
        if ($imageInfo[0] < 400 || $imageInfo[1] < 400) {
            throw new \Exception('Imagem muito pequena. Mínimo: 400x400 pixels.');
        }
    }

    /**
     * Gerar nome único para arquivo
     */
    private function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
               . '_' . Str::random(8) 
               . '.' . $extension;
    }

    /**
     * Garantir que diretório existe
     */
    private function ensureDirectoryExists(string $path): void
    {
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }
    }

    /**
     * Criar versão redimensionada
     */
    private function createResizedVersion($image, string $fileName, string $basePath, string $size): string
    {
        $dimensions = self::SIZES[$size];
        $resized = clone $image;
        
        // Redimensionar mantendo proporção
        $resized->resize($dimensions['width'], $dimensions['height'], function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize(); // Não aumentar se menor
        });
        
        $path = "{$basePath}/{$size}_{$fileName}";
        $this->saveOptimizedImage($resized, $path, $size);
        
        return $path;
    }

    /**
     * Criar thumbnail quadrado
     */
    private function createThumbnail($image, string $fileName, string $basePath): string
    {
        $dimensions = self::SIZES['thumbnail'];
        $thumbnail = clone $image;
        
        // Criar thumbnail quadrado centralizado
        $thumbnail->fit($dimensions['width'], $dimensions['height'], function ($constraint) {
            $constraint->upsize();
        });
        
        $path = "{$basePath}/thumb_{$fileName}";
        $this->saveOptimizedImage($thumbnail, $path, 'thumbnail');
        
        return $path;
    }

    /**
     * Salvar imagem otimizada
     */
    private function saveOptimizedImage($image, string $path, string $size): void
    {
        // Converter para formato mais eficiente se necessário
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Aplicar compressão baseada no tamanho
        $quality = match($size) {
            'thumbnail' => 75,
            'small' => 80,
            'medium' => 85,
            default => self::QUALITY
        };
        
        // Remover metadados desnecessários
        $image->stripImage();
        
        // Salvar com compressão otimizada
        if (in_array($extension, ['jpg', 'jpeg'])) {
            $image->encode('jpg', $quality);
        } elseif ($extension === 'png') {
            // PNG usa compressão sem perda
            $image->encode('png', 9);
        } elseif ($extension === 'webp') {
            $image->encode('webp', $quality);
        }
        
        Storage::disk('public')->put($path, $image->stream());
    }

    /**
     * Obter metadados da imagem
     */
    private function getImageMetadata($image, UploadedFile $file): array
    {
        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'aspect_ratio' => round($image->width() / $image->height(), 2),
        ];
    }

    /**
     * Deletar todas as versões de uma imagem
     */
    public function deleteProductImages(string $basePath): void
    {
        if (Storage::disk('public')->exists($basePath)) {
            Storage::disk('public')->deleteDirectory($basePath);
        }
    }

    /**
     * Otimizar imagem existente
     */
    public function optimizeExistingImage(string $path): void
    {
        if (!Storage::disk('public')->exists($path)) {
            return;
        }
        
        $fullPath = Storage::disk('public')->path($path);
        $image = Image::make($fullPath);
        
        // Otimizar
        $image->stripImage();
        $image->encode(null, self::QUALITY);
        
        // Salvar sobrescrevendo
        Storage::disk('public')->put($path, $image->stream());
    }
}