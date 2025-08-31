<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Services\ImageDownloadService;
use Illuminate\Console\Command;

class DownloadCategoryImages extends Command
{
    protected $signature = 'categories:download-images 
                           {--force : Força o download mesmo se a categoria já tiver imagem}
                           {--category= : Baixa imagem apenas para uma categoria específica}
                           {--limit=10 : Limite de downloads por execução}';
                           
    protected $description = 'Baixa imagens de alta qualidade para categorias usando APIs externas';

    protected ImageDownloadService $imageService;

    public function __construct(ImageDownloadService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    public function handle(): int
    {
        $this->info('🖼️  Iniciando download de imagens para categorias...');
        
        // Verificar configurações
        if (!$this->checkConfiguration()) {
            return Command::FAILURE;
        }

        $force = $this->option('force');
        $specificCategory = $this->option('category');
        $limit = (int) $this->option('limit');

        // Buscar categorias
        $query = Category::query();
        
        if ($specificCategory) {
            $query->where('slug', $specificCategory);
        } elseif (!$force) {
            $query->whereNull('image_path');
        }
        
        $categories = $query->limit($limit)->get();

        if ($categories->isEmpty()) {
            if ($specificCategory) {
                $this->error("❌ Categoria '{$specificCategory}' não encontrada.");
            } else {
                $this->info('✅ Todas as categorias já possuem imagens!');
            }
            return Command::SUCCESS;
        }

        $this->info("📋 Encontradas {$categories->count()} categorias para processar");
        
        $progressBar = $this->output->createProgressBar($categories->count());
        $progressBar->start();

        $successCount = 0;
        $failureCount = 0;
        $errors = [];

        foreach ($categories as $category) {
            $progressBar->advance();
            
            try {
                $result = $this->processCategory($category, $force);
                
                if ($result['success']) {
                    $successCount++;
                    $this->newLine();
                    $this->info("✅ {$category->name}: {$result['message']}");
                } else {
                    $failureCount++;
                    $errors[] = "{$category->name}: {$result['message']}";
                    $this->newLine();
                    $this->warn("⚠️  {$category->name}: {$result['message']}");
                }
                
            } catch (\Exception $e) {
                $failureCount++;
                $errors[] = "{$category->name}: {$e->getMessage()}";
                $this->newLine();
                $this->error("❌ {$category->name}: {$e->getMessage()}");
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Resumo
        $this->info("🎉 Processo concluído!");
        $this->table(
            ['Status', 'Quantidade'],
            [
                ['✅ Sucessos', $successCount],
                ['⚠️  Falhas', $failureCount],
                ['📊 Total', $categories->count()]
            ]
        );

        // Mostrar erros se houver
        if (!empty($errors)) {
            $this->newLine();
            $this->error('❌ Erros encontrados:');
            foreach ($errors as $error) {
                $this->line("  • {$error}");
            }
            $this->newLine();
            $this->comment('💡 Dicas:');
            $this->line('  • Verifique sua conexão com a internet');
            $this->line('  • Confirme as chaves de API no arquivo .env');
            $this->line('  • Execute novamente com --force para tentar baixar novamente');
        }

        return $failureCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Processa uma categoria individual
     */
    private function processCategory(Category $category, bool $force): array
    {
        // Verificar se já tem imagem
        if (!$force && $category->image_path) {
            return [
                'success' => true,
                'message' => 'Já possui imagem'
            ];
        }

        // Tentar baixar imagem
        $imagePath = $this->imageService->downloadCategoryImage($category->slug);
        
        if ($imagePath) {
            // Atualizar categoria
            $category->update(['image_path' => $imagePath]);
            
            return [
                'success' => true,
                'message' => "Imagem baixada: {$imagePath}"
            ];
        }

        // Se falhou, gerar placeholder
        $placeholderUrl = $this->imageService->generatePlaceholder($category->slug);
        
        return [
            'success' => false,
            'message' => "Não foi possível baixar imagem. Placeholder disponível: {$placeholderUrl}"
        ];
    }

    /**
     * Verifica se as configurações necessárias estão presentes
     */
    private function checkConfiguration(): bool
    {
        $unsplashKey = config('services.unsplash.access_key');
        $pexelsKey = config('services.pexels.api_key');

        if (!$unsplashKey && !$pexelsKey) {
            $this->error('❌ Nenhuma chave de API configurada!');
            $this->newLine();
            $this->comment('💡 Configure pelo menos uma das opções no arquivo .env:');
            $this->line('');
            $this->line('# Para Unsplash (recomendado)');
            $this->line('UNSPLASH_ACCESS_KEY=sua_chave_aqui');
            $this->line('');
            $this->line('# Para Pexels (alternativa)');
            $this->line('PEXELS_API_KEY=sua_chave_aqui');
            $this->line('');
            $this->comment('🔗 Obtenha suas chaves em:');
            $this->line('  • Unsplash: https://unsplash.com/developers');
            $this->line('  • Pexels: https://www.pexels.com/api/');
            
            return false;
        }

        if (!$unsplashKey) {
            $this->warn('⚠️  Chave do Unsplash não configurada. Usando apenas Pexels.');
        }

        if (!$pexelsKey) {
            $this->warn('⚠️  Chave do Pexels não configurada. Usando apenas Unsplash.');
        }

        return true;
    }
}