<?php
/**
 * Arquivo: database/seeders/HighVolumeSeeder.php
 * Descrição: Seeder para popular banco com ALTO VOLUME de dados para testes de performance
 * Laravel Version: 12.x
 * Criado em: 02/09/2025
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerProfile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class HighVolumeSeeder extends Seeder
{
    protected $faker;
    
    public function __construct()
    {
        $this->faker = Faker::create('pt_BR');
    }

    /**
     * Run the database seeds - ALTO VOLUME
     */
    public function run(): void
    {
        echo "\n🚀 INICIANDO SEEDER DE ALTO VOLUME...\n";
        echo str_repeat("=", 80) . "\n";
        echo "⚠️  ATENÇÃO: Este seeder criará milhares de registros!\n";
        echo "    • ~100 categorias\n";
        echo "    • ~500 vendedores\n";
        echo "    • ~10.000 produtos\n";
        echo "    • ~2.000 clientes\n";
        echo str_repeat("=", 80) . "\n";
        
        $startTime = microtime(true);
        
        // Aumentar tempo limite
        set_time_limit(300); // 5 minutos
        
        // 1. Categorias completas
        $this->createCategoriesHighVolume();
        
        // 2. Vendedores em massa
        $this->createSellersHighVolume();
        
        // 3. Produtos em massa
        $this->createProductsHighVolume();
        
        // 4. Clientes em massa
        $this->createCustomersHighVolume();
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        $this->showHighVolumeStats($executionTime);
    }

    /**
     * Criar categorias completas - ALTO VOLUME
     */
    private function createCategoriesHighVolume(): void
    {
        echo "\n📂 CRIANDO CATEGORIAS COMPLETAS (ALTO VOLUME)...\n";
        
        $categories = [
            'Eletrônicos e Tecnologia' => [
                'icon' => 'fas fa-laptop',
                'subcategories' => [
                    'Smartphones e Celulares', 'Notebooks e Computadores', 'Tablets e E-readers',
                    'TVs e Home Theater', 'Áudio e Som', 'Câmeras e Fotografia', 
                    'Games e Consoles', 'Smartwatches e Wearables', 'Acessórios Tech',
                    'Componentes PC', 'Redes e WiFi', 'Cabos e Adaptadores',
                    'Carregadores e Baterias', 'Drones e Robótica', 'Casa Inteligente'
                ]
            ],
            'Moda e Vestuário' => [
                'icon' => 'fas fa-tshirt',
                'subcategories' => [
                    'Roupas Masculinas', 'Roupas Femininas', 'Roupas Infantis',
                    'Calçados Masculinos', 'Calçados Femininos', 'Calçados Infantis',
                    'Bolsas e Carteiras', 'Acessórios de Moda', 'Relógios e Joias',
                    'Óculos de Sol', 'Lingerie e Pijamas', 'Roupas Esportivas',
                    'Moda Praia', 'Uniformes Profissionais', 'Artigos de Couro'
                ]
            ],
            'Casa e Construção' => [
                'icon' => 'fas fa-home',
                'subcategories' => [
                    'Móveis para Sala', 'Móveis para Quarto', 'Móveis para Cozinha',
                    'Decoração e Presentes', 'Iluminação', 'Cama Mesa e Banho',
                    'Jardim e Piscina', 'Ferramentas e Construção', 'Materiais Elétricos',
                    'Tintas e Vernizes', 'Pisos e Azulejos', 'Organização',
                    'Climatização', 'Segurança Residencial', 'Limpeza Doméstica'
                ]
            ],
            'Esportes e Fitness' => [
                'icon' => 'fas fa-dumbbell',
                'subcategories' => [
                    'Academia e Fitness', 'Futebol e Society', 'Basquete e Vôlei',
                    'Natação e Esportes Aquáticos', 'Ciclismo', 'Corrida e Caminhada',
                    'Artes Marciais', 'Camping e Trilha', 'Pesca e Caça',
                    'Tênis e Squash', 'Skateboard e Patins', 'Suplementos Esportivos',
                    'Roupas Esportivas', 'Equipamentos de Ginástica'
                ]
            ],
            'Saúde e Beleza' => [
                'icon' => 'fas fa-heart',
                'subcategories' => [
                    'Perfumes Femininos', 'Perfumes Masculinos', 'Maquiagem',
                    'Cuidados com o Cabelo', 'Cuidados com a Pele', 'Higiene Pessoal',
                    'Suplementos e Vitaminas', 'Equipamentos de Saúde', 'Dermocosméticos',
                    'Protetor Solar', 'Cuidados com o Corpo', 'Unhas e Manicure',
                    'Aparelhos de Beleza', 'Produtos Naturais'
                ]
            ],
            'Automotivo' => [
                'icon' => 'fas fa-car',
                'subcategories' => [
                    'Pneus e Rodas', 'Som Automotivo', 'GPS e Eletrônicos',
                    'Acessórios para Carros', 'Ferramentas Automotivas', 'Óleos e Lubrificantes',
                    'Peças e Componentes', 'Limpeza Automotiva', 'Segurança Veicular',
                    'Moto Peças', 'Perfumes Automotivos', 'Capas e Proteção'
                ]
            ],
            'Livros e Papelaria' => [
                'icon' => 'fas fa-book',
                'subcategories' => [
                    'Literatura Nacional', 'Literatura Estrangeira', 'Livros Técnicos',
                    'Livros Infantis', 'Didáticos e Educação', 'HQs e Mangás',
                    'Material Escolar', 'Material de Escritório', 'Cadernos e Agendas',
                    'Canetas e Lápis', 'Arte e Artesanato', 'Impressão e Cópias'
                ]
            ],
            'Alimentos e Bebidas' => [
                'icon' => 'fas fa-utensils',
                'subcategories' => [
                    'Mercearia Básica', 'Bebidas Alcoólicas', 'Bebidas não Alcoólicas',
                    'Produtos Naturais', 'Doces e Chocolates', 'Cafés Premium',
                    'Chás e Infusões', 'Produtos Diet e Light', 'Suplementos Alimentares',
                    'Temperos e Condimentos', 'Produtos Orgânicos', 'Congelados'
                ]
            ],
            'Pet Shop' => [
                'icon' => 'fas fa-paw',
                'subcategories' => [
                    'Ração para Cães', 'Ração para Gatos', 'Petiscos e Treats',
                    'Brinquedos Pet', 'Camas e Casinhas', 'Coleiras e Guias',
                    'Higiene e Banho', 'Medicamentos Pet', 'Aquários e Peixes',
                    'Pássaros e Aves', 'Roedores', 'Acessórios Pet'
                ]
            ],
            'Brinquedos e Games' => [
                'icon' => 'fas fa-gamepad',
                'subcategories' => [
                    'Bonecas e Acessórios', 'Carrinhos e Veículos', 'Jogos de Tabuleiro',
                    'Quebra-cabeças', 'Blocos de Montar', 'Pelúcias',
                    'Brinquedos Educativos', 'Jogos Eletrônicos', 'Brinquedos de Praia',
                    'Instrumentos Musicais', 'Fantasias e Disfarces'
                ]
            ]
        ];

        $sortOrder = 1;
        foreach ($categories as $parentName => $data) {
            echo "  📁 Criando: $parentName\n";
            
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                    'parent_id' => null,
                    'description' => "Categoria completa de $parentName com produtos variados"
                ]
            );

            $subOrder = 1;
            foreach ($data['subcategories'] as $subName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($subName)],
                    [
                        'name' => $subName,
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'sort_order' => $subOrder++,
                        'description' => "Subcategoria de $subName"
                    ]
                );
            }
        }
        
        echo "✅ " . Category::count() . " categorias criadas\n";
    }

    /**
     * Criar vendedores em massa - ALTO VOLUME
     */
    private function createSellersHighVolume(): void
    {
        echo "\n👥 CRIANDO VENDEDORES EM MASSA (500 vendedores)...\n";
        
        $businessPrefixes = [
            'Tech', 'Digital', 'Smart', 'Pro', 'Ultra', 'Super', 'Mega', 'Max',
            'Prime', 'Premium', 'Elite', 'Master', 'Gold', 'Silver', 'Platinum',
            'Express', 'Rapid', 'Fast', 'Quick', 'Instant', 'Direct', 'Easy'
        ];
        
        $businessSuffixes = [
            'Store', 'Shop', 'Market', 'Center', 'Plaza', 'Mall', 'Hub',
            'Point', 'Zone', 'World', 'Land', 'City', 'House', 'Home'
        ];

        $cities = [
            ['name' => 'São Paulo', 'state' => 'SP'],
            ['name' => 'Rio de Janeiro', 'state' => 'RJ'],
            ['name' => 'Belo Horizonte', 'state' => 'MG'],
            ['name' => 'Porto Alegre', 'state' => 'RS'],
            ['name' => 'Curitiba', 'state' => 'PR'],
            ['name' => 'Salvador', 'state' => 'BA'],
            ['name' => 'Brasília', 'state' => 'DF'],
            ['name' => 'Fortaleza', 'state' => 'CE'],
            ['name' => 'Recife', 'state' => 'PE'],
            ['name' => 'Manaus', 'state' => 'AM'],
            ['name' => 'Goiânia', 'state' => 'GO'],
            ['name' => 'Campinas', 'state' => 'SP'],
            ['name' => 'Florianópolis', 'state' => 'SC'],
            ['name' => 'Vitória', 'state' => 'ES'],
        ];

        // Usar DB::transaction para melhor performance
        DB::transaction(function () use ($businessPrefixes, $businessSuffixes, $cities) {
            for ($i = 1; $i <= 500; $i++) {
                $prefix = $businessPrefixes[array_rand($businessPrefixes)];
                $suffix = $businessSuffixes[array_rand($businessSuffixes)];
                $businessName = $prefix . ' ' . $suffix;
                $city = $cities[array_rand($cities)];
                
                $user = User::create([
                    'name' => $businessName,
                    'email' => 'seller' . $i . '@marketplace.com',
                    'password' => Hash::make('seller123'),
                    'role' => 'seller',
                    'email_verified_at' => $this->faker->optional(0.9)->dateTimeBetween('-1 year', 'now'),
                    'is_active' => $this->faker->boolean(95),
                    'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                ]);

                SellerProfile::create([
                    'user_id' => $user->id,
                    'company_name' => $businessName,
                    'document_type' => 'cnpj',
                    'document_number' => $this->faker->cnpj(false),
                    'phone' => $this->faker->cellphoneNumber(false),
                    'address' => $this->faker->streetAddress,
                    'city' => $city['name'],
                    'state' => $city['state'],
                    'postal_code' => $this->faker->postcode,
                    'bank_name' => $this->faker->randomElement(['Banco do Brasil', 'Itaú', 'Bradesco', 'Santander', 'Caixa']),
                    'bank_agency' => $this->faker->numerify('####'),
                    'bank_account' => $this->faker->numerify('#####-#'),
                    'status' => $this->faker->randomElement(['approved', 'approved', 'approved', 'approved', 'pending', 'rejected']), // 80% aprovados
                    'commission_rate' => $this->faker->randomFloat(2, 3, 15),
                    'product_limit' => $this->faker->randomElement([50, 100, 200, 500, 1000]),
                    'approved_at' => $this->faker->optional(0.8)->dateTimeBetween('-6 months', 'now'),
                    'submitted_at' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
                ]);
                
                if ($i % 50 == 0) {
                    echo "    ✓ $i vendedores criados\n";
                }
            }
        });
        
        echo "✅ " . SellerProfile::count() . " vendedores criados\n";
    }

    /**
     * Criar produtos em massa - ALTO VOLUME
     */
    private function createProductsHighVolume(): void
    {
        echo "\n📦 CRIANDO PRODUTOS EM MASSA (10.000 produtos)...\n";
        
        $productNames = [
            // Eletrônicos
            'Smartphone', 'Notebook', 'Tablet', 'Smart TV', 'Fone Bluetooth', 'Smartwatch',
            'Câmera Digital', 'Console', 'Teclado', 'Mouse', 'Monitor', 'Impressora',
            
            // Moda
            'Camiseta', 'Calça Jeans', 'Tênis', 'Vestido', 'Jaqueta', 'Shorts',
            'Blusa', 'Saia', 'Sandália', 'Bota', 'Bolsa', 'Carteira',
            
            // Casa
            'Sofá', 'Mesa', 'Cadeira', 'Cama', 'Guarda-roupa', 'Estante',
            'Luminária', 'Tapete', 'Almofada', 'Cortina', 'Espelho', 'Quadro',
            
            // Outros
            'Livro', 'Brinquedo', 'Perfume', 'Relógio', 'Óculos', 'Mochila',
        ];
        
        $brands = [
            'Samsung', 'Apple', 'Xiaomi', 'LG', 'Sony', 'Dell', 'HP', 'Lenovo',
            'Nike', 'Adidas', 'Puma', 'Zara', 'H&M', 'C&A', 'Renner',
            'Tok&Stok', 'Casas Bahia', 'Magazine Luiza', 'Americanas'
        ];
        
        $adjectives = [
            'Premium', 'Pro', 'Max', 'Ultra', 'Super', 'Mega', 'Smart', 'Digital',
            'Moderno', 'Clássico', 'Elegante', 'Confortável', 'Resistente', 'Portátil'
        ];

        $sellers = SellerProfile::where('status', 'approved')->pluck('id')->toArray();
        $categories = Category::whereNotNull('parent_id')->pluck('id')->toArray();
        
        if (empty($sellers) || empty($categories)) {
            echo "⚠️  Erro: Vendedores ou categorias não encontrados\n";
            return;
        }

        echo "    📊 Vendedores aprovados: " . count($sellers) . "\n";
        echo "    📊 Categorias disponíveis: " . count($categories) . "\n";
        
        // Criar produtos em lotes para melhor performance
        $batchSize = 100;
        $totalProducts = 10000;
        
        for ($batch = 0; $batch < $totalProducts / $batchSize; $batch++) {
            $products = [];
            
            for ($i = 0; $i < $batchSize; $i++) {
                $productName = $productNames[array_rand($productNames)];
                $brand = $brands[array_rand($brands)];
                $adjective = $adjectives[array_rand($adjectives)];
                $model = $this->faker->bothify('??-####');
                
                $fullName = $brand . ' ' . $productName . ' ' . $adjective . ' ' . $model;
                $price = $this->faker->randomFloat(2, 19.99, 4999.99);
                
                $products[] = [
                    'seller_id' => $sellers[array_rand($sellers)],
                    'category_id' => $categories[array_rand($categories)],
                    'name' => $fullName,
                    'slug' => Str::slug($fullName) . '-' . Str::random(6),
                    'description' => $this->generateRealisticDescription($productName, $brand),
                    'short_description' => $this->faker->sentence(rand(10, 20)),
                    'price' => $price,
                    'compare_at_price' => $this->faker->optional(0.4)->randomFloat(2, $price * 1.1, $price * 1.8),
                    'cost' => $price * $this->faker->randomFloat(2, 0.4, 0.7),
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'barcode' => $this->faker->ean13,
                    'stock_quantity' => $this->faker->numberBetween(0, 200),
                    'stock_status' => $this->faker->randomElement(['in_stock', 'in_stock', 'in_stock', 'in_stock', 'out_of_stock']),
                    'weight' => $this->faker->randomFloat(3, 0.05, 15),
                    'length' => $this->faker->randomFloat(2, 5, 120),
                    'width' => $this->faker->randomFloat(2, 5, 80),
                    'height' => $this->faker->randomFloat(2, 2, 60),
                    'status' => $this->faker->randomElement(['active', 'active', 'active', 'active', 'active', 'draft']),
                    'featured' => $this->faker->boolean(15), // 15% featured
                    'digital' => $this->faker->boolean(5),
                    'views_count' => $this->faker->numberBetween(0, 10000),
                    'sales_count' => $this->faker->numberBetween(0, 1000),
                    'rating_average' => $this->faker->optional(0.6)->randomFloat(1, 2.5, 5.0),
                    'rating_count' => $this->faker->numberBetween(0, 500),
                    'published_at' => $this->faker->optional(0.9)->dateTimeBetween('-1 year', 'now'),
                    'brand' => $brand,
                    'model' => $model,
                    'warranty_months' => $this->faker->randomElement([0, 3, 6, 12, 24, 36]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Inserir lote
            Product::insert($products);
            
            $currentTotal = ($batch + 1) * $batchSize;
            echo "    ✓ $currentTotal produtos criados\n";
        }
        
        echo "✅ " . Product::count() . " produtos criados no total\n";
    }

    /**
     * Criar clientes em massa - ALTO VOLUME
     */
    private function createCustomersHighVolume(): void
    {
        echo "\n🛍️ CRIANDO CLIENTES EM MASSA (2.000 clientes)...\n";
        
        $customers = [];
        for ($i = 1; $i <= 2000; $i++) {
            $customers[] = [
                'name' => $this->faker->name,
                'email' => 'customer' . $i . '@marketplace.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'email_verified_at' => $this->faker->optional(0.85)->dateTimeBetween('-1 year', 'now'),
                'is_active' => $this->faker->boolean(92),
                'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
            
            if ($i % 200 == 0) {
                User::insert($customers);
                $customers = [];
                echo "    ✓ $i clientes criados\n";
            }
        }
        
        // Inserir restantes
        if (!empty($customers)) {
            User::insert($customers);
        }
        
        echo "✅ " . User::where('role', 'customer')->count() . " clientes criados\n";
    }

    /**
     * Gerar descrição realística do produto
     */
    private function generateRealisticDescription($produto, $marca): string
    {
        $templates = [
            "O $marca $produto é a escolha ideal para quem busca qualidade e performance. Desenvolvido com tecnologia de ponta, oferece recursos avançados que atendem às suas necessidades diárias.",
            "Apresentamos o novo $marca $produto, um produto revolucionário que combina design moderno com funcionalidade superior. Perfeito para uso profissional e pessoal.",
            "Descubra a excelência do $marca $produto. Com acabamento premium e durabilidade comprovada, é o investimento certo para seu estilo de vida.",
        ];
        
        $features = [
            "Design ergonômico e moderno",
            "Alta durabilidade e resistência",
            "Tecnologia de última geração",
            "Fácil instalação e uso",
            "Garantia estendida inclusa",
            "Suporte técnico especializado",
            "Certificação de qualidade internacional",
            "Material premium de primeira linha"
        ];
        
        $description = $templates[array_rand($templates)] . "\n\n";
        $description .= "PRINCIPAIS CARACTERÍSTICAS:\n";
        
        $selectedFeatures = array_rand($features, rand(3, 5));
        foreach ($selectedFeatures as $index) {
            $description .= "• " . $features[$index] . "\n";
        }
        
        $description .= "\nGARANTIA E SUPORTE:\n";
        $description .= "• Garantia do fabricante\n";
        $description .= "• Suporte técnico via chat e telefone\n";
        $description .= "• Manual em português incluso\n";
        
        return $description;
    }

    /**
     * Exibir estatísticas finais de alto volume
     */
    private function showHighVolumeStats(float $executionTime): void
    {
        echo "\n🎉 SEEDER DE ALTO VOLUME CONCLUÍDO!\n";
        echo str_repeat("=", 80) . "\n";
        
        $stats = [
            'Categorias' => Category::count(),
            'Vendedores' => SellerProfile::count(),
            'Produtos' => Product::count(),
            'Clientes' => User::where('role', 'customer')->count(),
            'Total Usuários' => User::count(),
        ];
        
        echo "📊 ESTATÍSTICAS FINAIS:\n";
        foreach ($stats as $label => $count) {
            echo sprintf("├── %-15s: %s\n", $label, number_format($count));
        }
        
        // Estatísticas de produtos
        $activeProducts = Product::where('status', 'active')->count();
        $featuredProducts = Product::where('featured', true)->count();
        $approvedSellers = SellerProfile::where('status', 'approved')->count();
        
        echo "\n📦 DETALHES DOS PRODUTOS:\n";
        echo sprintf("├── Produtos Ativos: %s\n", number_format($activeProducts));
        echo sprintf("├── Produtos Destaque: %s\n", number_format($featuredProducts));
        echo sprintf("└── Vendedores Aprovados: %s\n", number_format($approvedSellers));
        
        echo "\n⏱️ PERFORMANCE:\n";
        echo sprintf("├── Tempo de execução: %s segundos\n", $executionTime);
        echo sprintf("├── Registros/segundo: %s\n", number_format(array_sum($stats) / $executionTime));
        echo sprintf("└── Tamanho estimado DB: %s\n", $this->estimateDbSize());
        
        echo "\n🎯 SISTEMA PRONTO PARA TESTES DE ALTA CARGA!\n";
        echo str_repeat("=", 80) . "\n";
    }

    /**
     * Estimar tamanho do banco
     */
    private function estimateDbSize(): string
    {
        try {
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS db_size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            
            return isset($result[0]) ? $result[0]->db_size_mb . ' MB' : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}