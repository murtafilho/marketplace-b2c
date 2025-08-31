<?php
/**
 * Arquivo: database/seeders/CategorySeeder.php
 * Descrição: Seeder para categorias do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Eletrônicos',
                'slug' => 'eletronicos',
                'description' => 'Smartphones, tablets, computadores, acessórios eletrônicos e gadgets',
                'image_path' => null,
                'sort_order' => 1,
                'subcategories' => [
                    'Smartphones e Celulares',
                    'Tablets',
                    'Computadores e Notebooks',
                    'Acessórios para Celular',
                    'Fones de Ouvido',
                    'Carregadores e Cabos',
                    'Smartwatch',
                    'Câmeras Digitais'
                ]
            ],
            [
                'name' => 'Roupas e Acessórios',
                'slug' => 'roupas-e-acessorios',
                'description' => 'Roupas masculinas, femininas, infantis e acessórios de moda',
                'image_path' => null,
                'sort_order' => 2,
                'subcategories' => [
                    'Roupas Femininas',
                    'Roupas Masculinas',
                    'Roupas Infantis',
                    'Calçados',
                    'Bolsas e Carteiras',
                    'Relógios',
                    'Joias e Bijuterias',
                    'Óculos'
                ]
            ],
            [
                'name' => 'Casa e Jardim',
                'slug' => 'casa-e-jardim',
                'description' => 'Móveis, decoração, utensílios domésticos e jardinagem',
                'image_path' => null,
                'sort_order' => 3,
                'subcategories' => [
                    'Móveis',
                    'Decoração',
                    'Cozinha e Mesa',
                    'Banheiro',
                    'Jardim e Piscina',
                    'Ferramentas',
                    'Iluminação',
                    'Organização'
                ]
            ],
            [
                'name' => 'Esportes e Fitness',
                'slug' => 'esportes-e-fitness',
                'description' => 'Equipamentos esportivos, roupas fitness e suplementos',
                'image_path' => null,
                'sort_order' => 4,
                'subcategories' => [
                    'Musculação e Fitness',
                    'Futebol',
                    'Natação',
                    'Corrida',
                    'Ciclismo',
                    'Artes Marciais',
                    'Suplementos',
                    'Roupas Esportivas'
                ]
            ],
            [
                'name' => 'Beleza e Cuidados',
                'slug' => 'beleza-e-cuidados',
                'description' => 'Cosméticos, produtos de higiene e cuidados pessoais',
                'image_path' => null,
                'sort_order' => 5,
                'subcategories' => [
                    'Maquiagem',
                    'Cuidados com a Pele',
                    'Cabelos',
                    'Perfumes',
                    'Cuidados Masculinos',
                    'Higiene Pessoal',
                    'Unhas',
                    'Produtos Naturais'
                ]
            ],
            [
                'name' => 'Livros e Educação',
                'slug' => 'livros-e-educacao',
                'description' => 'Livros, cursos online, material escolar e educacional',
                'image_path' => null,
                'sort_order' => 6,
                'subcategories' => [
                    'Livros Físicos',
                    'E-books',
                    'Cursos Online',
                    'Material Escolar',
                    'Livros Técnicos',
                    'Literatura',
                    'Livros Infantis',
                    'Audiobooks'
                ]
            ],
            [
                'name' => 'Games e Entretenimento',
                'slug' => 'games-e-entretenimento',
                'description' => 'Jogos digitais, consoles, filmes e entretenimento',
                'image_path' => null,
                'sort_order' => 7,
                'subcategories' => [
                    'Jogos Digitais',
                    'Consoles',
                    'Acessórios para Games',
                    'Filmes Digitais',
                    'Streaming',
                    'Board Games',
                    'Brinquedos',
                    'Colecionáveis'
                ]
            ],
            [
                'name' => 'Automotivo',
                'slug' => 'automotivo',
                'description' => 'Peças, acessórios e produtos para veículos',
                'image_path' => null,
                'sort_order' => 8,
                'subcategories' => [
                    'Peças de Carro',
                    'Pneus',
                    'Acessórios Internos',
                    'Som Automotivo',
                    'Ferramentas Automotivas',
                    'Cuidados com o Carro',
                    'Motos',
                    'GPS e Eletrônicos'
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $subcategories = $categoryData['subcategories'];
            unset($categoryData['subcategories']);
            
            // Criar categoria principal (usando firstOrCreate para evitar duplicatas)
            $categoryData['is_active'] = true;
            $category = Category::firstOrCreate(
                ['slug' => $categoryData['slug']], // buscar por slug
                $categoryData // dados para criar se não existir
            );
            
            // Criar subcategorias (usando firstOrCreate)
            foreach ($subcategories as $index => $subName) {
                $subSlug = Str::slug($subName);
                Category::firstOrCreate(
                    ['slug' => $subSlug], // buscar por slug
                    [
                        'name' => $subName,
                        'slug' => $subSlug,
                        'description' => "Subcategoria de {$category->name}",
                        'parent_id' => $category->id,
                        'is_active' => true,
                        'sort_order' => $index + 1
                    ]
                );
            }
        }

        echo "✅ " . Category::count() . " categorias criadas com sucesso!\n";
    }
}
