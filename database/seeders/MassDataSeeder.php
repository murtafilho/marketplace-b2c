<?php
/**
 * Arquivo: database/seeders/MassDataSeeder.php
 * Descrição: Seeder para popular banco com dados em massa para testes
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Models\SellerProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MassDataSeeder extends Seeder
{
    protected $faker;
    
    public function __construct()
    {
        $this->faker = Faker::create('pt_BR');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🚀 INICIANDO SEEDER DE DADOS EM MASSA...\n";
        echo str_repeat("=", 60) . "\n";
        
        // 1. Criar Categorias
        $this->createCategories();
        
        // 2. Criar Vendedores
        $this->createSellers();
        
        // 3. Criar Produtos
        $this->createProducts();
        
        // 4. Criar Clientes
        $this->createCustomers();
        
        echo "\n✅ SEEDER CONCLUÍDO COM SUCESSO!\n";
        echo str_repeat("=", 60) . "\n";
        $this->showStats();
    }

    /**
     * Criar estrutura de categorias
     */
    private function createCategories(): void
    {
        echo "\n📁 Criando Categorias...\n";
        
        $categories = [
            'Eletrônicos' => [
                'icon' => 'fa-tv',
                'subcategories' => [
                    'Celulares e Smartphones',
                    'Notebooks e Computadores',
                    'Tablets e E-readers',
                    'TVs e Home Theater',
                    'Áudio e Som',
                    'Câmeras e Drones',
                    'Games e Consoles',
                    'Acessórios de Informática',
                    'Smartwatches',
                ]
            ],
            'Moda e Vestuário' => [
                'icon' => 'fa-tshirt',
                'subcategories' => [
                    'Roupas Masculinas',
                    'Roupas Femininas',
                    'Roupas Infantis',
                    'Calçados',
                    'Bolsas e Mochilas',
                    'Acessórios',
                    'Relógios',
                    'Óculos',
                    'Joias e Bijuterias',
                ]
            ],
            'Casa e Decoração' => [
                'icon' => 'fa-home',
                'subcategories' => [
                    'Móveis',
                    'Decoração',
                    'Cama, Mesa e Banho',
                    'Iluminação',
                    'Organização',
                    'Jardim e Varanda',
                    'Cozinha',
                    'Banheiro',
                    'Ferramentas',
                ]
            ],
            'Esportes e Lazer' => [
                'icon' => 'fa-running',
                'subcategories' => [
                    'Fitness e Musculação',
                    'Futebol',
                    'Natação',
                    'Ciclismo',
                    'Camping e Trilha',
                    'Skate e Patins',
                    'Tênis e Squash',
                    'Artes Marciais',
                    'Pesca e Náutica',
                ]
            ],
            'Beleza e Cuidados' => [
                'icon' => 'fa-spa',
                'subcategories' => [
                    'Perfumes',
                    'Maquiagem',
                    'Cuidados com Cabelo',
                    'Cuidados com a Pele',
                    'Cuidados Masculinos',
                    'Dermocosméticos',
                    'Equipamentos de Beleza',
                ]
            ],
            'Livros e Papelaria' => [
                'icon' => 'fa-book',
                'subcategories' => [
                    'Livros Físicos',
                    'E-books',
                    'Material Escolar',
                    'Material de Escritório',
                    'Artes e Crafts',
                    'Cadernos e Agendas',
                ]
            ],
            'Automotivo' => [
                'icon' => 'fa-car',
                'subcategories' => [
                    'Acessórios para Carros',
                    'Pneus',
                    'Som Automotivo',
                    'GPS e Eletrônicos',
                    'Ferramentas Automotivas',
                    'Óleos e Fluidos',
                    'Peças',
                ]
            ],
            'Alimentos e Bebidas' => [
                'icon' => 'fa-utensils',
                'subcategories' => [
                    'Mercearia',
                    'Bebidas',
                    'Alimentos Saudáveis',
                    'Doces e Chocolates',
                    'Cafés e Chás',
                    'Suplementos',
                ]
            ],
            'Pet Shop' => [
                'icon' => 'fa-paw',
                'subcategories' => [
                    'Ração para Cães',
                    'Ração para Gatos',
                    'Acessórios Pet',
                    'Higiene e Limpeza',
                    'Brinquedos Pet',
                    'Casinhas e Camas',
                    'Medicamentos',
                ]
            ],
            'Brinquedos' => [
                'icon' => 'fa-gamepad',
                'subcategories' => [
                    'Bonecas e Acessórios',
                    'Carrinhos e Veículos',
                    'Jogos e Quebra-cabeças',
                    'Pelúcias',
                    'Educativos',
                    'Playground',
                ]
            ],
        ];

        $sortOrder = 1;
        foreach ($categories as $parentName => $data) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'slug' => Str::slug($parentName),
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                    'parent_id' => null,
                ]
            );

            $subOrder = 1;
            foreach ($data['subcategories'] as $subName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($subName)],
                    [
                        'name' => $subName,
                        'slug' => Str::slug($subName),
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'sort_order' => $subOrder++,
                    ]
                );
            }
        }
        
        echo "✅ " . Category::count() . " categorias criadas\n";
    }

    /**
     * Criar vendedores com perfis aprovados
     */
    private function createSellers(): void
    {
        echo "\n👥 Criando Vendedores...\n";
        
        $businessTypes = [
            'Eletrônicos' => ['Tech', 'Digital', 'Smart', 'Info', 'Gadget'],
            'Moda' => ['Fashion', 'Style', 'Trend', 'Chic', 'Boutique'],
            'Casa' => ['Home', 'Decor', 'Design', 'Living', 'Casa'],
            'Esportes' => ['Sport', 'Fitness', 'Active', 'Pro', 'Athletic'],
            'Beleza' => ['Beauty', 'Glam', 'Care', 'Bella', 'Charm'],
        ];

        $cities = [
            'São Paulo' => 'SP',
            'Rio de Janeiro' => 'RJ',
            'Belo Horizonte' => 'MG',
            'Porto Alegre' => 'RS',
            'Curitiba' => 'PR',
            'Salvador' => 'BA',
            'Brasília' => 'DF',
            'Fortaleza' => 'CE',
            'Recife' => 'PE',
            'Manaus' => 'AM',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $businessType = array_rand($businessTypes);
            $suffix = $businessTypes[$businessType][array_rand($businessTypes[$businessType])];
            $city = array_rand($cities);
            
            $user = User::firstOrCreate(
                ['email' => 'seller' . $i . '@marketplace.com'],
                [
                    'name' => $this->faker->company . ' ' . $suffix,
                    'email' => 'seller' . $i . '@marketplace.com',
                    'password' => Hash::make('seller123'),
                    'role' => 'seller',
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            SellerProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'company_name' => $user->name,
                    'document_type' => 'CNPJ',
                    'document_number' => $this->faker->cnpj(false),
                    'phone' => $this->faker->cellphoneNumber(false),
                    'address' => $this->faker->streetAddress,
                    'city' => $city,
                    'state' => $cities[$city],
                    'postal_code' => $this->faker->postcode,
                    'bank_name' => $this->faker->randomElement(['Banco do Brasil', 'Itaú', 'Bradesco', 'Santander', 'Caixa']),
                    'bank_agency' => $this->faker->numerify('####'),
                    'bank_account' => $this->faker->numerify('#####-#'),
                    'status' => $this->faker->randomElement(['approved', 'approved', 'approved', 'pending_approval', 'rejected']),
                    'commission_rate' => $this->faker->randomFloat(2, 5, 15),
                    'product_limit' => $this->faker->randomElement([100, 200, 500, 1000]),
                    'approved_at' => $this->faker->optional(0.8)->dateTimeBetween('-6 months', 'now'),
                    'submitted_at' => $this->faker->dateTimeBetween('-7 months', '-6 months'),
                    'mp_connected' => $this->faker->boolean(70),
                    'mp_access_token' => $this->faker->optional(0.7)->uuid,
                ]
            );
        }
        
        echo "✅ " . SellerProfile::count() . " vendedores criados\n";
    }

    /**
     * Criar produtos com imagens e variações
     */
    private function createProducts(): void
    {
        echo "\n📦 Criando Produtos...\n";
        
        $productTemplates = [
            'Eletrônicos' => [
                ['nome' => 'Smartphone', 'prefixos' => ['Galaxy', 'iPhone', 'Xiaomi', 'Motorola'], 'preco_min' => 800, 'preco_max' => 5000],
                ['nome' => 'Notebook', 'prefixos' => ['Dell', 'Lenovo', 'Apple', 'Asus', 'HP'], 'preco_min' => 1500, 'preco_max' => 8000],
                ['nome' => 'Tablet', 'prefixos' => ['iPad', 'Galaxy Tab', 'Lenovo Tab'], 'preco_min' => 500, 'preco_max' => 3000],
                ['nome' => 'Smart TV', 'prefixos' => ['Samsung', 'LG', 'Sony', 'TCL'], 'preco_min' => 1000, 'preco_max' => 5000],
                ['nome' => 'Fone Bluetooth', 'prefixos' => ['AirPods', 'Galaxy Buds', 'JBL', 'Sony'], 'preco_min' => 100, 'preco_max' => 1500],
                ['nome' => 'Smartwatch', 'prefixos' => ['Apple Watch', 'Galaxy Watch', 'Mi Band'], 'preco_min' => 200, 'preco_max' => 3000],
            ],
            'Moda' => [
                ['nome' => 'Camiseta', 'prefixos' => ['Básica', 'Estampada', 'Polo', 'Regata'], 'preco_min' => 30, 'preco_max' => 150],
                ['nome' => 'Calça', 'prefixos' => ['Jeans', 'Sarja', 'Moletom', 'Social'], 'preco_min' => 80, 'preco_max' => 300],
                ['nome' => 'Tênis', 'prefixos' => ['Nike', 'Adidas', 'Puma', 'Vans'], 'preco_min' => 150, 'preco_max' => 800],
                ['nome' => 'Vestido', 'prefixos' => ['Casual', 'Festa', 'Longo', 'Midi'], 'preco_min' => 80, 'preco_max' => 500],
                ['nome' => 'Jaqueta', 'prefixos' => ['Jeans', 'Couro', 'Bomber', 'Corta-vento'], 'preco_min' => 150, 'preco_max' => 600],
            ],
            'Casa' => [
                ['nome' => 'Sofá', 'prefixos' => ['3 Lugares', '2 Lugares', 'Retrátil', 'Canto'], 'preco_min' => 800, 'preco_max' => 5000],
                ['nome' => 'Mesa', 'prefixos' => ['Jantar', 'Centro', 'Lateral', 'Escritório'], 'preco_min' => 300, 'preco_max' => 2000],
                ['nome' => 'Cadeira', 'prefixos' => ['Gamer', 'Escritório', 'Jantar'], 'preco_min' => 150, 'preco_max' => 1500],
                ['nome' => 'Luminária', 'prefixos' => ['LED', 'Pendente', 'Abajur', 'Spot'], 'preco_min' => 50, 'preco_max' => 500],
            ],
        ];

        $sellers = SellerProfile::where('status', 'approved')->get();
        $categories = Category::whereNotNull('parent_id')->get();
        
        $totalProducts = 0;
        foreach ($sellers as $seller) {
            $numProducts = rand(5, 20);
            
            for ($i = 0; $i < $numProducts; $i++) {
                $category = $categories->random();
                $parentCategory = $category->parent->name ?? 'Geral';
                
                // Selecionar template baseado na categoria
                $templates = $productTemplates[$parentCategory] ?? $productTemplates['Eletrônicos'];
                $template = $templates[array_rand($templates)];
                
                $prefixo = $template['prefixos'][array_rand($template['prefixos'])];
                $modelo = $this->faker->bothify('??-####');
                $price = $this->faker->randomFloat(2, $template['preco_min'], $template['preco_max']);
                
                $product = Product::create([
                    'seller_id' => $seller->id,
                    'category_id' => $category->id,
                    'name' => $prefixo . ' ' . $template['nome'] . ' ' . $modelo,
                    'slug' => Str::slug($prefixo . ' ' . $template['nome'] . ' ' . $modelo) . '-' . Str::random(6),
                    'description' => $this->generateProductDescription($template['nome'], $prefixo),
                    'short_description' => $this->faker->sentence(15),
                    'price' => $price,
                    'compare_at_price' => $this->faker->optional(0.3)->randomFloat(2, $price * 1.1, $price * 1.5),
                    'cost' => $price * 0.6,
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'barcode' => $this->faker->ean13,
                    'stock_quantity' => $this->faker->numberBetween(0, 100),
                    'stock_status' => $this->faker->randomElement(['in_stock', 'in_stock', 'in_stock', 'out_of_stock']),
                    'weight' => $this->faker->randomFloat(3, 0.1, 10),
                    'length' => $this->faker->randomFloat(2, 10, 100),
                    'width' => $this->faker->randomFloat(2, 10, 100),
                    'height' => $this->faker->randomFloat(2, 5, 50),
                    'status' => $this->faker->randomElement(['active', 'active', 'active', 'draft', 'inactive']),
                    'featured' => $this->faker->boolean(20),
                    'digital' => $this->faker->boolean(10),
                    'views_count' => $this->faker->numberBetween(0, 5000),
                    'sales_count' => $this->faker->numberBetween(0, 500),
                    'rating_average' => $this->faker->optional(0.7)->randomFloat(1, 3.5, 5.0),
                    'rating_count' => $this->faker->numberBetween(0, 200),
                    'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-3 months', 'now'),
                    'brand' => $prefixo,
                    'warranty_months' => $this->faker->randomElement([3, 6, 12, 24, 36]),
                ]);

                // Criar imagens do produto (1-3 imagens)
                $numImages = rand(1, 3);
                for ($j = 1; $j <= $numImages; $j++) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'original_name' => 'product-' . $product->id . '-' . $j . '.jpg',
                        'file_name' => 'product-' . $product->id . '-' . $j . '.jpg',
                        'file_path' => 'products/' . $product->id . '/product-' . $j . '.jpg',
                        'mime_type' => 'image/jpeg',
                        'file_size' => rand(100000, 500000),
                        'alt_text' => $product->name,
                        'sort_order' => $j,
                        'is_primary' => $j === 1,
                    ]);
                }

                // Criar variações para alguns produtos (30% de chance)
                if ($this->faker->boolean(30) && in_array($parentCategory, ['Moda', 'Eletrônicos'])) {
                    $this->createProductVariations($product);
                }

                $totalProducts++;
            }
        }
        
        echo "✅ $totalProducts produtos criados\n";
    }

    /**
     * Criar variações de produto
     */
    private function createProductVariations(Product $product): void
    {
        $variations = [];
        
        // Variações por categoria
        if (str_contains($product->name, 'Camiseta') || str_contains($product->name, 'Calça') || str_contains($product->name, 'Vestido')) {
            $sizes = ['P', 'M', 'G', 'GG', 'XG'];
            $colors = ['Preto', 'Branco', 'Azul', 'Vermelho', 'Verde', 'Cinza'];
            
            foreach ($sizes as $size) {
                foreach (array_slice($colors, 0, rand(2, 4)) as $color) {
                    $variations[] = [
                        'product_id' => $product->id,
                        'name' => 'Tamanho',
                        'value' => $size . ' - ' . $color,
                        'sku_suffix' => $size . '-' . substr($color, 0, 3),
                        'price_adjustment' => rand(-10, 20),
                        'weight_adjustment' => 0.0,
                        'stock_quantity' => rand(0, 30),
                        'meta_data' => ['size' => $size, 'color' => $color],
                        'is_active' => true,
                        'sort_order' => 1,
                    ];
                }
            }
        } elseif (str_contains($product->name, 'Smartphone') || str_contains($product->name, 'Notebook')) {
            $storages = ['64GB', '128GB', '256GB', '512GB', '1TB'];
            $colors = ['Preto', 'Branco', 'Prata', 'Dourado'];
            
            foreach (array_slice($storages, 0, rand(2, 4)) as $storage) {
                foreach (array_slice($colors, 0, rand(1, 3)) as $color) {
                    $variations[] = [
                        'product_id' => $product->id,
                        'name' => 'Armazenamento',
                        'value' => $storage . ' - ' . $color,
                        'sku_suffix' => $storage . '-' . substr($color, 0, 3),
                        'price_adjustment' => intval($storage) * 2,
                        'weight_adjustment' => 0.0,
                        'stock_quantity' => rand(0, 20),
                        'meta_data' => ['storage' => $storage, 'color' => $color],
                        'is_active' => true,
                        'sort_order' => 1,
                    ];
                }
            }
        }

        foreach ($variations as $variation) {
            ProductVariation::create($variation);
        }
    }

    /**
     * Criar clientes
     */
    private function createCustomers(): void
    {
        echo "\n🛍️ Criando Clientes...\n";
        
        for ($i = 1; $i <= 1000; $i++) {
            User::firstOrCreate(
                ['email' => 'customer' . $i . '@marketplace.com'],
                [
                    'name' => $this->faker->name,
                    'email' => 'customer' . $i . '@marketplace.com',
                    'password' => Hash::make('customer123'),
                    'role' => 'customer',
                    'email_verified_at' => $this->faker->optional(0.9)->dateTimeBetween('-1 year', 'now'),
                    'is_active' => $this->faker->boolean(95),
                    'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                ]
            );
        }
        
        echo "✅ 1000 clientes criados\n";
    }

    /**
     * Gerar descrição realista do produto
     */
    private function generateProductDescription($tipo, $marca): string
    {
        $introducao = [
            "Apresentamos o novo $marca $tipo, um produto de alta qualidade que vai revolucionar sua experiência.",
            "O $marca $tipo é a escolha perfeita para quem busca qualidade e desempenho superior.",
            "Descubra o incrível $marca $tipo, desenvolvido com tecnologia de ponta para atender suas necessidades.",
        ];

        $caracteristicas = [
            "Design moderno e elegante que combina com seu estilo.",
            "Fabricado com materiais premium de alta durabilidade.",
            "Tecnologia avançada para máximo desempenho.",
            "Ergonomicamente projetado para seu conforto.",
            "Certificado pelos principais órgãos de qualidade.",
        ];

        $beneficios = [
            "Garantia estendida para sua tranquilidade.",
            "Suporte técnico especializado disponível 24/7.",
            "Entrega rápida e segura para todo o Brasil.",
            "Satisfação garantida ou seu dinheiro de volta.",
            "Acompanha manual detalhado em português.",
        ];

        $descricao = $introducao[array_rand($introducao)] . "\n\n";
        $descricao .= "CARACTERÍSTICAS:\n";
        
        for ($i = 0; $i < 3; $i++) {
            $descricao .= "• " . $caracteristicas[array_rand($caracteristicas)] . "\n";
        }
        
        $descricao .= "\nBENEFÍCIOS:\n";
        for ($i = 0; $i < 2; $i++) {
            $descricao .= "• " . $beneficios[array_rand($beneficios)] . "\n";
        }

        return $descricao;
    }

    /**
     * Exibir estatísticas finais
     */
    private function showStats(): void
    {
        echo "\n📊 ESTATÍSTICAS FINAIS:\n";
        echo str_repeat("-", 40) . "\n";
        echo "• Categorias: " . Category::count() . "\n";
        echo "• Vendedores: " . SellerProfile::count() . "\n";
        echo "• Produtos: " . Product::count() . "\n";
        echo "• Variações: " . ProductVariation::count() . "\n";
        echo "• Imagens: " . ProductImage::count() . "\n";
        echo "• Clientes: " . User::where('role', 'customer')->count() . "\n";
        echo "• Total de Usuários: " . User::count() . "\n";
        echo str_repeat("-", 40) . "\n";
    }
}