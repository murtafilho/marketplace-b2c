<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Services\CategoryImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateDefaultCategoryImages extends Command
{
    protected $signature = 'categories:create-images';
    protected $description = 'Criar imagens padrão para categorias que não possuem imagem';

    protected CategoryImageService $imageService;

    public function __construct(CategoryImageService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    public function handle()
    {
        $this->info('🎨 Criando imagens padrão para categorias...');

        // Criar diretório se não existir
        if (!Storage::disk('public')->exists('categories')) {
            Storage::disk('public')->makeDirectory('categories');
            $this->info('📁 Diretório storage/app/public/categories criado');
        }

        $categoriesWithoutImages = Category::whereNull('image_path')->get();

        if ($categoriesWithoutImages->isEmpty()) {
            $this->info('✅ Todas as categorias já possuem imagens!');
            return Command::SUCCESS;
        }

        $this->info("📋 Encontradas {$categoriesWithoutImages->count()} categorias sem imagem");

        $defaultImages = $this->getDefaultImagePaths();
        $createdCount = 0;

        foreach ($categoriesWithoutImages as $category) {
            // Usar imagem específica se existir, senão criar dinamicamente
            $imagePath = $defaultImages[$category->slug] ?? "categories/{$category->slug}.jpg";
            
            // Criar arquivo de imagem se não existir
            if (!Storage::disk('public')->exists($imagePath)) {
                $this->createDefaultImage($category->slug, $imagePath);
            }

            // Atualizar categoria
            $category->update(['image_path' => $imagePath]);
            $createdCount++;
            
            $this->info("✅ {$category->name}: {$imagePath}");
        }

        $this->info("🎉 Processo concluído! {$createdCount} imagens criadas/atribuídas");
        
        return Command::SUCCESS;
    }

    private function getDefaultImagePaths(): array
    {
        return [
            'eletronicos' => 'categories/eletronicos.jpg',
            'roupas-e-acessorios' => 'categories/roupas-acessorios.jpg',
            'moda-e-vestuario' => 'categories/moda-vestuario.jpg',
            'casa-e-jardim' => 'categories/casa-jardim.jpg',
            'casa-e-decoracao' => 'categories/casa-decoracao.jpg',
            'esportes-e-fitness' => 'categories/esportes-fitness.jpg',
            'esportes-e-lazer' => 'categories/esportes-lazer.jpg',
            'beleza-e-cuidados' => 'categories/beleza-cuidados.jpg',
            'automotivo' => 'categories/automotivo.jpg',
            'livros-e-educacao' => 'categories/livros-educacao.jpg',
            'livros-e-papelaria' => 'categories/livros-papelaria.jpg',
            'games-e-entretenimento' => 'categories/games-entretenimento.jpg',
            'alimentos-e-bebidas' => 'categories/alimentos-bebidas.jpg',
            'pet-shop' => 'categories/pet-shop.jpg',
        ];
    }

    private function createDefaultImage(string $slug, string $imagePath): void
    {
        $color = $this->getCategoryColor($slug);

        // Criar imagem básica (placeholder)
        $imageContent = $this->createSimpleImage($slug, $color);
        Storage::disk('public')->put($imagePath, $imageContent);
    }

    private function getCategoryColor(string $slug): string
    {
        // Cores específicas para categorias conhecidas
        $specificColors = [
            'eletronicos' => '#3B82F6',
            'roupas-e-acessorios' => '#EC4899',
            'moda-e-vestuario' => '#EC4899',
            'casa-e-jardim' => '#10B981',
            'casa-e-decoracao' => '#10B981',
            'esportes-e-fitness' => '#F59E0B',
            'esportes-e-lazer' => '#F59E0B',
            'beleza-e-cuidados' => '#8B5CF6',
            'automotivo' => '#6B7280',
            'livros-e-educacao' => '#6366F1',
            'livros-e-papelaria' => '#6366F1',
            'games-e-entretenimento' => '#8B5CF6',
            'alimentos-e-bebidas' => '#EAB308',
            'pet-shop' => '#F59E0B',
        ];

        if (isset($specificColors[$slug])) {
            return $specificColors[$slug];
        }

        // Sistema inteligente baseado em palavras-chave
        $categoryTypes = [
            // Eletrônicos e Tecnologia
            'tecnologia' => [
                'keywords' => ['smartphone', 'celular', 'computador', 'notebook', 'tablet', 'camera', 'fone', 'carregador', 'smartwatch', 'eletronicos', 'audio', 'som'],
                'color' => '#3B82F6'
            ],
            
            // Moda e Vestuário
            'moda' => [
                'keywords' => ['roupa', 'calcado', 'bolsa', 'carteira', 'relogio', 'joia', 'bijuteria', 'oculos', 'acessorio', 'moda', 'feminina', 'masculina', 'infantil'],
                'color' => '#EC4899'
            ],
            
            // Casa e Decoração
            'casa' => [
                'keywords' => ['movel', 'decoracao', 'cozinha', 'mesa', 'banheiro', 'jardim', 'piscina', 'ferramenta', 'iluminacao', 'organizacao', 'cama', 'varanda'],
                'color' => '#10B981'
            ],
            
            // Esportes e Fitness
            'esporte' => [
                'keywords' => ['musculacao', 'fitness', 'futebol', 'natacao', 'corrida', 'ciclismo', 'artes-marciais', 'suplemento', 'esportiva', 'camping', 'trilha', 'skate', 'patins', 'tenis', 'squash', 'pesca', 'nautica'],
                'color' => '#F59E0B'
            ],
            
            // Beleza e Cuidados
            'beleza' => [
                'keywords' => ['maquiagem', 'pele', 'cabelo', 'perfume', 'masculino', 'higiene', 'unha', 'natural', 'dermocosmetico', 'equipamento'],
                'color' => '#8B5CF6'
            ],
            
            // Livros e Educação
            'educacao' => [
                'keywords' => ['livro', 'e-book', 'curso', 'material', 'escolar', 'tecnico', 'literatura', 'infantil', 'audiobook', 'escritorio', 'arte', 'craft', 'caderno', 'agenda'],
                'color' => '#6366F1'
            ],
            
            // Games e Entretenimento
            'games' => [
                'keywords' => ['jogo', 'console', 'game', 'filme', 'streaming', 'board', 'brinquedo', 'colecionavel', 'boneca', 'carrinho', 'veiculo', 'quebra-cabeca', 'pelucia', 'educativo', 'playground'],
                'color' => '#8B5CF6'
            ],
            
            // Automotivo
            'automotivo' => [
                'keywords' => ['peca', 'carro', 'pneu', 'interno', 'automotivo', 'moto', 'gps', 'oleo', 'fluido'],
                'color' => '#6B7280'
            ],
            
            // Alimentação e Bebidas
            'alimentacao' => [
                'keywords' => ['mercearia', 'bebida', 'saudavel', 'doce', 'chocolate', 'cafe', 'cha', 'alimento'],
                'color' => '#EAB308'
            ],
            
            // Pet Shop
            'pet' => [
                'keywords' => ['racao', 'cao', 'gato', 'pet', 'casinha', 'medicamento'],
                'color' => '#F97316'
            ]
        ];

        foreach ($categoryTypes as $type => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (strpos($slug, $keyword) !== false) {
                    return $data['color'];
                }
            }
        }

        // Cor padrão
        return '#22C55E';
    }

    private function createSimpleImage(string $slug, string $color): string
    {
        // Criar imagem JPEG válida usando GD
        $width = 300;
        $height = 300;
        
        // Converter cor hex para RGB
        $rgb = $this->hexToRgb($color);
        
        // Criar imagem
        $image = imagecreate($width, $height);
        
        // Definir cores
        $backgroundColor = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Preencher fundo
        imagefill($image, 0, 0, $backgroundColor);
        
        // Adicionar texto
        $text = strtoupper(str_replace('-', ' ', substr($slug, 0, 10)));
        $fontSize = 3;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        
        imagestring($image, $fontSize, $x, $y, $text, $textColor);
        
        // Capturar saída da imagem
        ob_start();
        imagejpeg($image, null, 80);
        $imageContent = ob_get_contents();
        ob_end_clean();
        
        // Limpar memória
        imagedestroy($image);
        
        return $imageContent;
    }
    
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }
}