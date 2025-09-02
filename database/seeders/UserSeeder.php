<?php
/**
 * Arquivo: database/seeders/UserSeeder.php
 * Descrição: Seeder para usuários e vendedores do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Database\Seeders;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. CRIAR ADMIN PRINCIPAL (se não existir)
        $admin = User::firstOrCreate(
            ['email' => 'admin@marketplace.com'],
            [
                'name' => 'Administrador Sistema',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]
        );

        echo "✅ Admin principal: admin@marketplace.com (já existia: " . ($admin->wasRecentlyCreated ? 'não' : 'sim') . ")\n";

        // 2. CRIAR VENDEDORES COM PERFIS COMPLETOS
        $sellers = [
            [
                'name' => 'Tech Store Brasil',
                'email' => 'tech@marketplace.com',
                'company_name' => 'Tech Store Brasil Ltda',
                'document_type' => 'cnpj',
                'document_number' => '12.345.678/0001-90',
                'phone' => '(11) 99999-1001',
                'city' => 'São Paulo',
                'state' => 'SP',
                'status' => 'approved'
            ],
            [
                'name' => 'Fashion House',
                'email' => 'fashion@marketplace.com',
                'company_name' => 'Fashion House Moda Ltda',
                'document_type' => 'cnpj', 
                'document_number' => '23.456.789/0001-01',
                'phone' => '(21) 99999-2002',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'status' => 'approved'
            ],
            [
                'name' => 'Casa & Decoração',
                'email' => 'casa@marketplace.com',
                'company_name' => 'Casa & Decoração Eireli',
                'document_type' => 'cnpj',
                'document_number' => '34.567.890/0001-12',
                'phone' => '(31) 99999-3003',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
                'status' => 'approved'
            ],
            [
                'name' => 'Sports Center',
                'email' => 'sports@marketplace.com',
                'company_name' => 'Sports Center Equipamentos',
                'document_type' => 'cnpj',
                'document_number' => '45.678.901/0001-23',
                'phone' => '(41) 99999-4004',
                'city' => 'Curitiba',
                'state' => 'PR',
                'status' => 'approved'
            ],
            [
                'name' => 'Beauty World',
                'email' => 'beauty@marketplace.com',
                'company_name' => 'Beauty World Cosméticos',
                'document_type' => 'cnpj',
                'document_number' => '56.789.012/0001-34',
                'phone' => '(51) 99999-5005',
                'city' => 'Porto Alegre',
                'state' => 'RS',
                'status' => 'approved'
            ],
            [
                'name' => 'Livraria Digital',
                'email' => 'livros@marketplace.com',
                'company_name' => 'Livraria Digital Educação',
                'document_type' => 'cnpj',
                'document_number' => '67.890.123/0001-45',
                'phone' => '(85) 99999-6006',
                'city' => 'Fortaleza',
                'state' => 'CE',
                'status' => 'approved'
            ],
            [
                'name' => 'Game Station',
                'email' => 'games@marketplace.com',
                'company_name' => 'Game Station Entretenimento',
                'document_type' => 'cnpj',
                'document_number' => '78.901.234/0001-56',
                'phone' => '(62) 99999-7007',
                'city' => 'Goiânia',
                'state' => 'GO',
                'status' => 'approved'
            ],
            [
                'name' => 'Auto Peças Express',
                'email' => 'auto@marketplace.com',
                'company_name' => 'Auto Peças Express Ltda',
                'document_type' => 'cnpj',
                'document_number' => '89.012.345/0001-67',
                'phone' => '(61) 99999-8008',
                'city' => 'Brasília',
                'state' => 'DF',
                'status' => 'approved'
            ],
            [
                'name' => 'Vendedor Pendente',
                'email' => 'pendente@marketplace.com',
                'company_name' => 'Loja Aguardando Aprovação',
                'document_type' => 'cpf',
                'document_number' => '123.456.789-10',
                'phone' => '(48) 99999-9009',
                'city' => 'Florianópolis',
                'state' => 'SC',
                'status' => 'pending'
            ],
            [
                'name' => 'Vendedor Rejeitado',
                'email' => 'rejeitado@marketplace.com',
                'company_name' => 'Loja com Problemas',
                'document_type' => 'cpf',
                'document_number' => '987.654.321-00',
                'phone' => '(84) 99999-0010',
                'city' => 'Natal',
                'state' => 'RN',
                'status' => 'rejected'
            ]
        ];

        foreach ($sellers as $index => $sellerData) {
            // Criar ou encontrar usuário
            $user = User::firstOrCreate(
                ['email' => $sellerData['email']],
                [
                    'name' => $sellerData['name'],
                    'password' => Hash::make('seller123'),
                    'role' => 'seller',
                    'email_verified_at' => now()
                ]
            );

            // Criar ou atualizar perfil do vendedor
            $profile = SellerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'document_type' => $sellerData['document_type'],
                    'document_number' => $sellerData['document_number'],
                    'company_name' => $sellerData['company_name'],
                    'phone' => $sellerData['phone'],
                    'address' => 'Rua Exemplo, ' . (100 + $index),
                    'city' => $sellerData['city'],
                    'state' => $sellerData['state'],
                    'postal_code' => sprintf('%05d-%03d', rand(10000, 99999), rand(100, 999)),
                    'bank_name' => 'Banco do Brasil',
                    'bank_account' => sprintf('%04d-%06d-%d', rand(1000, 9999), rand(100000, 999999), rand(0, 9)),
                    'status' => $sellerData['status'],
                    'commission_rate' => rand(10, 20) + (rand(0, 99) / 100), // 10.xx% a 20.xx%
                    'product_limit' => rand(100, 500),
                    'approved_at' => $sellerData['status'] === 'approved' ? now() : null,
                    'submitted_at' => now(),
                    'rejection_reason' => $sellerData['status'] === 'rejected' ? 'Documentação incompleta' : null
                ]
            );

            $status = $user->wasRecentlyCreated ? 'novo' : 'existente';
            echo "✅ Seller {$status}: {$sellerData['email']} ({$sellerData['status']})\n";
        }

        // 3. CRIAR ALGUNS CUSTOMERS DE TESTE
        $customersCreated = 0;
        
        for ($i = 1; $i <= 20; $i++) {
            $email = "cliente{$i}@marketplace.com";
            
            // Pular se for o email protegido cliente@marketplace.com
            if ($email === 'cliente@marketplace.com') {
                continue;
            }
            
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Cliente Teste {$i}",
                    'password' => Hash::make('cliente123'),
                    'role' => 'customer',
                    'email_verified_at' => rand(0, 1) ? now() : null // Alguns não verificados
                ]
            );
            
            if ($user->wasRecentlyCreated) {
                $customersCreated++;
            }
        }

        echo "✅ {$customersCreated} customers novos criados\n";

        // RESUMO
        $adminCount = User::where('role', 'admin')->count();
        $sellerCount = User::where('role', 'seller')->count();
        $customerCount = User::where('role', 'customer')->count();
        $profileCount = SellerProfile::count();

        echo "\n📊 RESUMO:\n";
        echo "- Admins: {$adminCount}\n";
        echo "- Sellers: {$sellerCount} (com perfis: {$profileCount})\n";
        echo "- Customers: {$customerCount}\n";
        echo "- Total de usuários: " . User::count() . "\n";
    }
}