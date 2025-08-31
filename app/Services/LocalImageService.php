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
        /** @var \Intervention\Image\Image $image */
        /** @var \Intervention\Image\Image */
        $image = Image::make($file);
        
        // Obter orientação correta da imagem
        $image->rotate(0); // Ensure correct image orientation using rotate(0)
        
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
            if (!array_key_exists($fileName, $dbImages)) {
                Storage::disk('public')->delete($file);
            }
        }
    }
    
    /**
     * Verificar espaço disponível em disco
     */
    public function checkDiskSpace($requiredBytes = 0): bool
    {
        $freeSpace = disk_free_space(storage_path('app/public'));
        $minFreeSpace = 100 * 1024 * 1024; // 100MB mínimo
        
        return ($freeSpace - $requiredBytes) > $minFreeSpace;
    }
    
    /**
     * Obter informações de uma imagem
     */
    public function getImageInfo(UploadedFile $file): array
    {
        $image = Image::make($file);
        
        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'original_name' => $file->getClientOriginalName()
        ];
    }
    
    /**
     * Validar se a imagem atende aos requisitos
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];
        $info = $this->getImageInfo($file);
        
        // Validar dimensões mínimas
        if ($info['width'] < 300 || $info['height'] < 300) {
            $errors[] = 'Imagem deve ter pelo menos 300x300 pixels';
        }
        
        // Validar dimensões máximas
        if ($info['width'] > 4000 || $info['height'] > 4000) {
            $errors[] = 'Imagem não pode exceder 4000x4000 pixels';
        }
        
        // Validar tamanho do arquivo
        if ($info['size'] > 5 * 1024 * 1024) { // 5MB
            $errors[] = 'Arquivo não pode exceder 5MB';
        }
        
        // Validar tipo MIME
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($info['mime_type'], $allowedMimes)) {
            $errors[] = 'Tipo de arquivo não suportado. Use JPEG, PNG ou WebP';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'info' => $info
        ];
    }
    
    /**
     * Criar thumbnail rápido para preview
     */
    public function createQuickThumbnail(UploadedFile $file, int $size = 150): string
    {
        $image = Image::make($file);
        $image->fit($size, $size);
        
        $tempPath = storage_path('app/public/temp/thumb_' . Str::random(20) . '.jpg');
        
        // Garantir que o diretório temp existe
        if (!file_exists(storage_path('app/public/temp'))) {
            mkdir(storage_path('app/public/temp'), 0755, true);
        }
        
        $image->save($tempPath, 80);
        
        return $tempPath;
    }
    
    /**
     * Limpar arquivos temporários antigos
     */
    public function cleanupTempFiles(int $maxAgeHours = 1): int
    {
        $tempPath = storage_path('app/public/temp');
        
        if (!is_dir($tempPath)) {
            return 0;
        }
        
        $files = glob($tempPath . '/*');
        $now = time();
        $maxAge = $maxAgeHours * 3600;
        $deletedCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= $maxAge) {
                    if (unlink($file)) {
                        $deletedCount++;
                    }
                }
            }
        }
        
        return $deletedCount;
    }
    
    /**
     * Gerar nome único para arquivo
     */
    public function generateUniqueFileName(UploadedFile $file, string $prefix = ''): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitizar nome base
        $baseName = Str::slug($baseName);
        
        // Criar nome único
        $fileName = $prefix . ($prefix ? '-' : '') . $baseName . '-' . Str::random(10) . '-' . time();
        
        return $fileName . '.' . $extension;
    }
    
    /**
     * Formatar tamanho de arquivo para exibição
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Verificar se uma imagem é válida
     */
    public function isValidImage(UploadedFile $file): bool
    {
        try {
            $image = Image::make($file);
            return $image->width() > 0 && $image->height() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Obter estatísticas do storage
     */
    public function getStorageStats(): array
    {
        $storagePath = storage_path('app/public');
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        
        return [
            'total_space' => $this->formatFileSize($totalSpace),
            'used_space' => $this->formatFileSize($usedSpace),
            'free_space' => $this->formatFileSize($freeSpace),
            'usage_percentage' => round(($usedSpace / $totalSpace) * 100, 2)
        ];
    }
}