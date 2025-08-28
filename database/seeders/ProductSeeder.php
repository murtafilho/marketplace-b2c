<?php
/**
 * Arquivo: database/seeders/ProductSeeder.php
 * Descrição: Seeder para produtos do marketplace - CARGA PESADA
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter sellers aprovados e categorias
        $sellers = SellerProfile::where('status', 'approved')->get();
        $categories = Category::whereNotNull('parent_id')->get(); // Apenas subcategorias
        
        if ($sellers->isEmpty() || $categories->isEmpty()) {
            echo "❌ Erro: É necessário ter sellers aprovados e categorias!\n";
            return;
        }

        echo "🚀 Iniciando criação de produtos em massa...\n";
        echo "👥 Sellers disponíveis: {$sellers->count()}\n";
        echo "📂 Categorias disponíveis: {$categories->count()}\n\n";

        // Templates de produtos por categoria principal
        $productTemplates = [
            'Eletrônicos' => [
                'iPhone 15 Pro Max 256GB',
                'Samsung Galaxy S24 Ultra',
                'MacBook Pro 16" M3',
                'iPad Pro 12.9" 512GB',
                'AirPods Pro 2ª Geração',
                'Apple Watch Ultra 2',
                'Dell XPS 13 Plus',
                'Sony WH-1000XM5',
                'Canon EOS R6 Mark II',
                'Xiaomi 13T Pro'
            ],
            'Roupas e Acessórios' => [
                'Tênis Nike Air Max 270',
                'Camiseta Polo Ralph Lauren',
                'Jeans Levi\'s 501 Original',
                'Bolsa Michael Kors',
                'Relógio Fossil Gen 6',
                'Óculos Ray-Ban Aviador',
                'Vestido Calvin Klein',
                'Sapato Social Democrata',
                'Mochila JanSport',
                'Carteira Tommy Hilfiger'
            ],
            'Casa e Jardim' => [
                'Sofá 3 Lugares Retrátil',
                'Mesa de Jantar 6 Cadeiras',
                'Geladeira Brastemp Frost Free',
                'Micro-ondas Electrolux 30L',
                'Aspirador Robô Roomba',
                'Conjunto de Panelas Tramontina',
                'Colchão King Size Ortobom',
                'Rack para TV até 65"',
                'Churrasqueira Elétrica',
                'Kit Jardinagem Completo'
            ],
            'Esportes e Fitness' => [
                'Esteira Elétrica Dream Fitness',
                'Bicicleta Caloi Elite Carbon',
                'Kit Musculação Completo',
                'Whey Protein Optimum Gold',
                'Tênis Adidas Ultraboost 22',
                'Bola Nike Futebol Campo',
                'Raquete Wilson Pro Staff',
                'Kimono Jiu-Jitsu Koral',
                'Luvas Boxing Everlast',
                'Suplemento Creatina 300g'
            ],
            'Beleza e Cuidados' => [
                'Base Fenty Beauty',
                'Perfume Chanel N°5 100ml',
                'Kit Skincare The Ordinary',
                'Babyliss Pro Titanium',
                'Escova Alisadora Mondial',
                'Máscara Facial L\'Oréal',
                'Batom MAC Ruby Woo',
                'Shampoo Pantene 400ml',
                'Creme Hidratante Nivea',
                'Kit Barba Bozzano'
            ],
            'Livros e Educação' => [
                'Curso Python Completo',
                'Livro: Clean Code',
                'E-book Marketing Digital',
                'Curso Excel Avançado',
                'Livro: O Investidor Inteligente',
                'Curso de Inglês Online',
                'Livro: Sapiens - Harari',
                'Kit Material Escolar',
                'Curso Photoshop CC',
                'Audiobook Pai Rico Pai Pobre'
            ],
            'Games e Entretenimento' => [
                'PlayStation 5 Standard',
                'Xbox Series X',
                'Nintendo Switch OLED',
                'The Last of Us Part II',
                'FIFA 24 Ultimate Edition',
                'Controle DualSense PS5',
                'Headset Gamer HyperX',
                'Cadeira Gamer DXRacer',
                'Mouse Gamer Logitech G502',
                'Teclado Mecânico Corsair'
            ],
            'Automotivo' => [
                'Pneu Michelin 205/55 R16',
                'Bateria Moura 60Ah',
                'Óleo Motor Castrol GTX',
                'Som Automotivo Pioneer',
                'Alarme Positron Cyber',
                'Kit Xenon H4 8000K',
                'Película Solar 3M',
                'Tapete Borracha Universal',
                'Cabo Chupeta 3 Metros',
                'GPS Garmin Nuvi'
            ]
        ];

        $totalProducts = 0;
        $batchSize = 100; // Processar em lotes

        foreach ($productTemplates as $mainCategory => $products) {
            echo "📦 Criando produtos para categoria: {$mainCategory}\n";
            
            // Buscar subcategorias desta categoria principal
            $mainCat = Category::where('name', $mainCategory)->whereNull('parent_id')->first();
            if (!$mainCat) continue;
            
            $subcategories = Category::where('parent_id', $mainCat->id)->get();
            if ($subcategories->isEmpty()) continue;

            $batch = [];
            
            foreach ($products as $productName) {
                // Criar múltiplas variações do mesmo produto para diferentes sellers
                foreach ($sellers->take(rand(2, 4)) as $seller) {
                    $category = $subcategories->random();
                    $basePrice = $this->generatePrice($mainCategory);
                    $hasDiscount = rand(0, 100) < 30; // 30% chance de ter desconto
                    
                    $product = [
                        'seller_id' => $seller->id,
                        'category_id' => $category->id,
                        'name' => $productName,
                        'slug' => Str::slug($productName . '-' . $seller->id),
                        'description' => $this->generateDescription($productName, $mainCategory),
                        'short_description' => $this->generateShortDescription($productName),
                        'price' => $basePrice,
                        'compare_at_price' => $hasDiscount ? $basePrice * rand(120, 150) / 100 : null,
                        'cost' => $basePrice * rand(60, 80) / 100,
                        'sku' => strtoupper(Str::random(3) . '-' . rand(1000, 9999)),
                        'barcode' => rand(1000000000000, 9999999999999),
                        'stock_quantity' => rand(0, 100),
                        'stock_status' => $this->getStockStatus(),
                        'weight' => rand(100, 5000) / 100, // 1g a 50kg
                        'length' => rand(10, 100),
                        'width' => rand(10, 100), 
                        'height' => rand(5, 50),
                        'status' => $this->getProductStatus(),
                        'featured' => rand(0, 100) < 15, // 15% são destaque
                        'digital' => $this->isDigital($mainCategory),
                        'downloadable_files' => $this->isDigital($mainCategory) ? json_encode(['file1.pdf', 'bonus.zip']) : null,
                        'meta_title' => $productName . ' - Melhor Preço',
                        'meta_description' => 'Compre ' . $productName . ' com melhor preço e entrega rápida',
                        'meta_keywords' => $this->generateKeywords($productName, $mainCategory),
                        'views_count' => rand(0, 1000),
                        'sales_count' => rand(0, 50),
                        'rating_average' => rand(350, 500) / 100, // 3.5 a 5.0
                        'rating_count' => rand(0, 100),
                        'published_at' => rand(0, 1) ? now()->subDays(rand(0, 30)) : null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $batch[] = $product;
                    
                    if (count($batch) >= $batchSize) {
                        DB::table('products')->insert($batch);
                        $totalProducts += count($batch);
                        echo "  ✅ {$totalProducts} produtos criados...\n";
                        $batch = [];
                    }
                }
            }
            
            // Inserir produtos restantes do lote
            if (!empty($batch)) {
                DB::table('products')->insert($batch);
                $totalProducts += count($batch);
            }
        }

        echo "\n🎉 CRIAÇÃO DE PRODUTOS CONCLUÍDA!\n";
        echo "📊 Total de produtos criados: {$totalProducts}\n";
        echo "🏪 Distribuídos entre " . $sellers->count() . " sellers\n";
        echo "📂 Em " . $categories->count() . " categorias\n\n";

        // Estatísticas finais
        $this->showStatistics();
    }

    private function generatePrice(string $category): float
    {
        $priceRanges = [
            'Eletrônicos' => [500, 8000],
            'Roupas e Acessórios' => [50, 800], 
            'Casa e Jardim' => [100, 3000],
            'Esportes e Fitness' => [80, 2000],
            'Beleza e Cuidados' => [20, 500],
            'Livros e Educação' => [30, 500],
            'Games e Entretenimento' => [200, 4000],
            'Automotivo' => [50, 1500]
        ];

        $range = $priceRanges[$category] ?? [50, 500];
        return rand($range[0], $range[1]) + (rand(0, 99) / 100);
    }

    private function generateDescription(string $name, string $category): string
    {
        $descriptions = [
            "Experimente a qualidade superior do {$name}. Produto original com garantia.",
            "O {$name} que você estava procurando está aqui! Entrega rápida e segura.",
            "Descubra o melhor {$name} do mercado. Qualidade garantida e preço justo.",
            "Não perca esta oportunidade única de adquirir o {$name} com desconto especial.",
            "O {$name} ideal para suas necessidades. Confira as especificações completas."
        ];

        return $descriptions[array_rand($descriptions)] . "\n\nCaracterísticas:\n• Alta qualidade\n• Garantia de 12 meses\n• Entrega em todo Brasil\n• Suporte técnico especializado";
    }

    private function generateShortDescription(string $name): string
    {
        $templates = [
            "{$name} original com garantia e entrega rápida",
            "O melhor {$name} com preço imperdível",
            "{$name} de alta qualidade para você",
            "Adquira seu {$name} com segurança e qualidade"
        ];

        return $templates[array_rand($templates)];
    }

    private function getStockStatus(): string
    {
        $statuses = ['in_stock', 'in_stock', 'in_stock', 'out_of_stock', 'backorder'];
        return $statuses[array_rand($statuses)];
    }

    private function getProductStatus(): string
    {
        $statuses = ['active', 'active', 'active', 'active', 'draft', 'inactive'];
        return $statuses[array_rand($statuses)];
    }

    private function isDigital(string $category): bool
    {
        $digitalCategories = ['Livros e Educação', 'Games e Entretenimento'];
        return in_array($category, $digitalCategories) && rand(0, 100) < 40; // 40% chance para essas categorias
    }

    private function generateKeywords(string $name, string $category): string
    {
        $keywords = [
            strtolower($name),
            strtolower($category),
            'comprar',
            'melhor preço',
            'promoção',
            'entrega rápida',
            'garantia'
        ];

        return implode(', ', array_slice($keywords, 0, 5));
    }

    private function showStatistics(): void
    {
        echo "📈 ESTATÍSTICAS FINAIS:\n";
        echo "═══════════════════════\n";
        
        $total = Product::count();
        $active = Product::where('status', 'active')->count();
        $featured = Product::where('featured', true)->count();
        $digital = Product::where('digital', true)->count();
        $inStock = Product::where('stock_status', 'in_stock')->count();
        
        echo "Total de produtos: {$total}\n";
        echo "Produtos ativos: {$active}\n";
        echo "Produtos em destaque: {$featured}\n";
        echo "Produtos digitais: {$digital}\n";
        echo "Em estoque: {$inStock}\n";
        
        // Por categoria
        echo "\nPor categoria:\n";
        $categories = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as total'))
            ->groupBy('categories.name')
            ->orderBy('total', 'desc')
            ->get();
            
        foreach ($categories->take(5) as $cat) {
            echo "- {$cat->name}: {$cat->total}\n";
        }

        echo "\n🏆 CARGA COMPLETA REALIZADA COM SUCESSO!\n";
    }
}