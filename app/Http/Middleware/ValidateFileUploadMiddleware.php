<?php
/**
 * Arquivo: app/Http/Middleware/ValidateFileUploadMiddleware.php
 * Descrição: Middleware para validação segura de uploads
 * Laravel Version: 12.x
 * Criado em: 29/08/2025
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateFileUploadMiddleware
{
    /**
     * Extensões permitidas por tipo
     */
    protected array $allowedExtensions = [
        'image' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
        'document' => ['pdf', 'doc', 'docx'],
        'general' => ['jpg', 'jpeg', 'png', 'webp', 'pdf']
    ];

    /**
     * Tamanhos máximos por tipo (em bytes)
     */
    protected array $maxSizes = [
        'image' => 5 * 1024 * 1024, // 5MB
        'document' => 10 * 1024 * 1024, // 10MB
        'general' => 5 * 1024 * 1024 // 5MB
    ];

    /**
     * MimeTypes permitidos
     */
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/webp',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type = 'general'): Response
    {
        // Verificar se há arquivos na requisição
        if (!$request->hasFile('files') && !$this->hasAnyFile($request)) {
            return $next($request);
        }

        // Validar todos os arquivos
        $files = $this->getAllFiles($request);
        
        foreach ($files as $fieldName => $file) {
            if ($file && $file->isValid()) {
                // Validar extensão
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = $this->allowedExtensions[$type] ?? $this->allowedExtensions['general'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    return response()->json([
                        'error' => "Extensão '{$extension}' não permitida para o campo '{$fieldName}'. Permitidas: " . implode(', ', $allowedExtensions)
                    ], 422);
                }

                // Validar tamanho
                $maxSize = $this->maxSizes[$type] ?? $this->maxSizes['general'];
                if ($file->getSize() > $maxSize) {
                    $maxSizeMB = round($maxSize / 1024 / 1024, 1);
                    return response()->json([
                        'error' => "Arquivo '{$fieldName}' excede o tamanho máximo de {$maxSizeMB}MB"
                    ], 422);
                }

                // Validar MIME type
                if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
                    return response()->json([
                        'error' => "Tipo de arquivo '{$file->getMimeType()}' não permitido para o campo '{$fieldName}'"
                    ], 422);
                }

                // Validação adicional de conteúdo para imagens
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    if (!$this->isValidImage($file)) {
                        return response()->json([
                            'error' => "Arquivo '{$fieldName}' não é uma imagem válida"
                        ], 422);
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Verificar se há algum arquivo na requisição
     */
    protected function hasAnyFile(Request $request): bool
    {
        foreach ($request->files->all() as $file) {
            if ($file) return true;
        }
        return false;
    }

    /**
     * Obter todos os arquivos da requisição
     */
    protected function getAllFiles(Request $request): array
    {
        $files = [];
        
        foreach ($request->files->all() as $fieldName => $file) {
            if (is_array($file)) {
                foreach ($file as $index => $subFile) {
                    $files["{$fieldName}[{$index}]"] = $subFile;
                }
            } else {
                $files[$fieldName] = $file;
            }
        }
        
        return $files;
    }

    /**
     * Validar se é uma imagem real
     */
    protected function isValidImage($file): bool
    {
        try {
            $imageInfo = getimagesize($file->getPathname());
            return $imageInfo !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}