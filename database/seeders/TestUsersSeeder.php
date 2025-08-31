<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Seed usuários de teste para desenvolvimento
     */
    public function run(): void
    {
        $this->command->info('🚀 Criando usuários de teste...');

        // 1. ADMIN DE TESTE
        $admin = $this->createAdmin();
        
        // 2. VENDEDOR DE TESTE
        $seller = $this->createSeller();
        
        // 3. CLIENTE DE TESTE
        $customer = $this->createCustomer();
        
        // 4. CRIAR CATEGORIA DE TESTE
        $category = $this->createCategory();
        
        // 5. CRIAR PRODUTO DE TESTE
        $this->createProduct($seller->sellerProfile, $category);

        $this->command->info('✅ Usuários de teste criados com sucesso!');
        $this->command->line('');
        $this->command->line('📋 CREDENCIAIS DE TESTE:');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('👑 ADMIN:    admin@marketplace.com | senha: admin123');
        $this->command->line('🏪 VENDEDOR: vendedor@marketplace.com | senha: vendedor123');
        $this->command->line('🛒 CLIENTE:  cliente@marketplace.com | senha: cliente123');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');
    }

    private function createAdmin(): User
    {
        $this->command->info('👑 Criando administrador...');
        
        return User::firstOrCreate(
            ['email' => 'admin@marketplace.com'],
            [
                'name' => 'Administrador Teste',
                'email' => 'admin@marketplace.com',
                'phone' => '(11) 99999-0001',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }

    private function createSeller(): User
    {
        $this->command->info('🏪 Criando vendedor...');
        
        $seller = User::firstOrCreate(
            ['email' => 'vendedor@marketplace.com'],
            [
                'name' => 'João Silva Vendedor',
                'email' => 'vendedor@marketplace.com',
                'phone' => '(11) 98765-4321',
                'role' => 'seller',
                'password' => Hash::make('vendedor123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // Criar perfil de vendedor se não existir
        if (!$seller->sellerProfile) {
            SellerProfile::create([
                'user_id' => $seller->id,
                'company_name' => 'Silva Comércio e Eletrônicos Ltda',
                'document_type' => 'cnpj',
                'document_number' => '12.345.678/0001-90',
                'phone' => '(11) 98765-4321',
                'address' => 'Rua das Flores, 123, Centro, São Paulo, SP, 01234-567',
                'city' => 'São Paulo',
                'state' => 'SP',
                'postal_code' => '01234-567',
                'bank_name' => 'Banco do Brasil',
                'bank_agency' => '1234',
                'bank_account' => '123456-7',
                'status' => 'approved', // Já aprovado para facilitar testes
                'commission_rate' => 8.5, // Comissão especial
                'product_limit' => 100, // Limite de produtos
                'approved_at' => now(),
                'submitted_at' => now()->subDays(2),
                'mp_connected' => false, // Ainda não conectou MP
            ]);
        }

        return $seller->load('sellerProfile');
    }

    private function createCustomer(): User
    {
        $this->command->info('🛒 Criando cliente...');
        
        return User::firstOrCreate(
            ['email' => 'cliente@marketplace.com'],
            [
                'name' => 'Maria Santos Cliente',
                'email' => 'cliente@marketplace.com',
                'phone' => '(11) 95555-5555',
                'role' => 'customer',
                'password' => Hash::make('cliente123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
    }

    private function createCategory(): Category
    {
        $this->command->info('📱 Criando categoria de teste...');
        
        return Category::firstOrCreate(
            ['slug' => 'eletronicos'],
            [
                'name' => 'Eletrônicos',
                'slug' => 'eletronicos',
                'description' => 'Produtos eletrônicos, smartphones, tablets e acessórios',
                'is_active' => true,
            ]
        );
    }

    private function createProduct(SellerProfile $sellerProfile, Category $category): Product
    {
        $this->command->info('📦 Criando produto de teste...');
        
        return Product::firstOrCreate(
            ['slug' => 'smartphone-samsung-galaxy-teste'],
            [
                'seller_id' => $sellerProfile->id,
                'category_id' => $category->id,
                'name' => 'Smartphone Samsung Galaxy A54 128GB',
                'slug' => 'smartphone-samsung-galaxy-teste',
                'description' => 'Smartphone Samsung Galaxy A54 com 128GB de armazenamento, câmera tripla de 50MP, tela de 6.4 polegadas Super AMOLED e bateria de 5000mAh. Produto em perfeito estado para testes do marketplace.',
                'short_description' => 'Smartphone Samsung Galaxy A54 128GB - Perfeito para testes',
                'price' => 1299.90,
                'compare_at_price' => 1599.90,
                'stock_quantity' => 15,
                'stock_status' => 'in_stock',
                'sku' => 'SAMSUNG-A54-128GB-TEST',
                'weight' => 202, // gramas
                'dimensions' => '159.9 x 74.7 x 8.2 mm',
                'status' => 'active',
                'featured' => true,
                'brand' => 'Samsung',
                'model' => 'Galaxy A54',
                'warranty_months' => 12,
                'published_at' => now(),
            ]
        );
    }
}