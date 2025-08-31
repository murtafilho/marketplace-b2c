<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Exception;

/**
 * Serviço Seguro de Upload
 * 
 * Resolve problemas sistemáticos de upload evitando:
 * - getRealPath() errors
 * - Laravel Storage adapter issues
 * - Path cannot be empty errors
 */
class SafeUploadService
{
    /**
     * Upload de documento (PDF, images)
     */
    public function uploadDocument(UploadedFile $file, string $directory): array
    {
        $this->validateFile($file, ['pdf', 'jpg', 'jpeg', 'png'], 2048); // 2MB max
        
        return $this->performUpload($file, $directory, 'document');
    }
    
    /**
     * Upload de imagem de produto
     */
    public function uploadProductImage(UploadedFile $file, int $productId): array
    {
        $this->validateFile($file, ['jpg', 'jpeg', 'png', 'webp'], 5120); // 5MB max
        
        $directory = "products/{$productId}";
        $result = $this->performUpload($file, $directory, 'image');
        
        // Criar thumbnail
        $result['thumbnail_path'] = $this->createThumbnail($file, $directory, $result['file_name']);
        $result['metadata'] = $this->getImageMetadata($file);
        
        return $result;
    }
    
    /**
     * Upload de avatar/logo
     */
    public function uploadAvatar(UploadedFile $file, string $directory): array
    {
        $this->validateFile($file, ['jpg', 'jpeg', 'png'], 1024); // 1MB max
        
        return $this->performUpload($file, $directory, 'avatar');
    }
    
    /**
     * Realizar upload de forma segura (sem Laravel Storage)
     */
    private function performUpload(UploadedFile $file, string $directory, string $type): array
    {
        // Gerar nome único
        $fileName = $this->generateFileName($file, $type);
        
        // Preparar diretórios
        $storagePath = storage_path('app/public');
        $destinationPath = $storagePath . DIRECTORY_SEPARATOR . $directory;
        
        // Criar diretório se não existir
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        // Caminho completo do arquivo
        $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;
        
        // Upload usando método nativo PHP (mais confiável)
        if (!$file->move($destinationPath, $fileName)) {
            throw new Exception('Falha ao fazer upload do arquivo');
        }
        
        // Verificar se o arquivo foi salvo
        if (!file_exists($fullPath)) {
            throw new Exception('Arquivo não foi salvo corretamente');
        }
        
        return [
            'file_name' => $fileName,
            'file_path' => $directory . '/' . $fileName,
            'full_path' => $fullPath,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];
    }
    
    /**
     * Criar thumbnail de imagem
     */
    private function createThumbnail(UploadedFile $file, string $directory, string $fileName): string
    {
        $thumbnailName = 'thumb_' . $fileName;
        
        // Para versão simples, apenas copiar o arquivo
        // Em produção, aqui seria usado ImageMagick ou GD
        $storagePath = storage_path('app/public');
        $sourcePath = $storagePath . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $fileName;
        $thumbnailPath = $storagePath . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $thumbnailName;
        
        if (file_exists($sourcePath)) {
            copy($sourcePath, $thumbnailPath);
        }
        
        return $directory . '/' . $thumbnailName;
    }
    
    /**
     * Obter metadados da imagem
     */
    private function getImageMetadata(UploadedFile $file): array
    {
        // Usar tmp_name ao invés de getPathname() para evitar problemas
        $tempPath = $file->getPathname();
        
        // Verificar se o arquivo temporário ainda existe
        if (file_exists($tempPath)) {
            $imageInfo = @getimagesize($tempPath);
            if ($imageInfo) {
                return [
                    'width' => $imageInfo[0],
                    'height' => $imageInfo[1],
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ];
            }
        }
        
        // Fallback se não conseguir obter dimensões
        return [
            'width' => 0,
            'height' => 0,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ];
    }
    
    /**
     * Validar arquivo
     */
    private function validateFile(UploadedFile $file, array $allowedTypes, int $maxSizeKB): void
    {
        if (!$file->isValid()) {
            throw new Exception('Arquivo inválido: ' . $this->getUploadErrorMessage($file->getError()));
        }
        
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedTypes)) {
            throw new Exception('Tipo de arquivo não permitido. Permitidos: ' . implode(', ', $allowedTypes));
        }
        
        $maxSizeBytes = $maxSizeKB * 1024;
        if ($file->getSize() > $maxSizeBytes) {
            throw new Exception("Arquivo muito grande. Máximo: {$maxSizeKB}KB");
        }
    }
    
    /**
     * Gerar nome único para arquivo
     */
    private function generateFileName(UploadedFile $file, string $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $baseName);
        
        return $type . '_' . substr($cleanName, 0, 20) . '_' . time() . '_' . Str::random(6) . '.' . $extension;
    }
    
    /**
     * Obter mensagem de erro de upload
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'Arquivo maior que o limite do servidor',
            UPLOAD_ERR_FORM_SIZE => 'Arquivo maior que o limite do formulário',
            UPLOAD_ERR_PARTIAL => 'Upload incompleto',
            UPLOAD_ERR_NO_FILE => 'Nenhum arquivo enviado',
            UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário não encontrado',
            UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo',
            UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensão',
            default => 'Erro desconhecido no upload'
        };
    }
    
    /**
     * Deletar arquivo
     */
    public function deleteFile(string $relativePath): bool
    {
        $fullPath = storage_path('app/public/' . $relativePath);
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true; // Já não existe
    }
    
    /**
     * Verificar se arquivo existe
     */
    public function fileExists(string $relativePath): bool
    {
        $fullPath = storage_path('app/public/' . $relativePath);
        return file_exists($fullPath);
    }
}