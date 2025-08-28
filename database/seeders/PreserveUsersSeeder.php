<?php
/**
 * Arquivo: database/seeders/PreserveUsersSeeder.php
 * Descrição: Seeder para preservar usuários importantes do sistema
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace Database\Seeders;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PreserveUsersSeeder extends Seeder
{
    /**
     * Usuários essenciais que devem sempre existir no sistema
     */
    private array $essentialUsers = [
        [
            'name' => 'Administrador Sistema',
            'email' => 'admin@marketplace.com',
            'password' => 'admin123',
            'role' => 'admin',
            'email_verified_at' => true,
        ],
        [
            'name' => 'Tech Store Brasil',
            'email' => 'tech@marketplace.com', 
            'password' => 'seller123',
            'role' => 'seller',
            'email_verified_at' => true,
            'seller_profile' => [
                'document_type' => 'cnpj',
                'document_number' => '12.345.678/0001-90',
                'company_name' => 'Tech Store Brasil Ltda',
                'phone' => '(11) 99999-1001',
                'address' => 'Rua Exemplo, 100',
                'city' => 'São Paulo',
                'state' => 'SP',
                'postal_code' => '01234-567',
                'bank_name' => 'Banco do Brasil',
                'bank_account' => '1234-567890-1',
                'status' => 'approved',
                'commission_rate' => 15.00,
                'product_limit' => 500,
                'approved_at' => true,
                'submitted_at' => true,
            ]
        ],
        [
            'name' => 'Cliente Teste',
            'email' => 'cliente@marketplace.com',
            'password' => 'cliente123', 
            'role' => 'customer',
            'email_verified_at' => true,
        ]
    ];

    /**
     * Execute o seeder de preservação
     */
    public function run(): void
    {
        echo "🔒 PRESERVANDO USUÁRIOS ESSENCIAIS\n";
        echo str_repeat("=", 50) . "\n";

        DB::transaction(function () {
            foreach ($this->essentialUsers as $userData) {
                $this->createOrUpdateUser($userData);
            }
        });

        echo "✅ USUÁRIOS ESSENCIAIS PRESERVADOS COM SUCESSO!\n";
        echo "🔑 Total de usuários preservados: " . count($this->essentialUsers) . "\n\n";
    }

    /**
     * Cria ou atualiza um usuário essencial
     */
    private function createOrUpdateUser(array $userData): void
    {
        $sellerData = $userData['seller_profile'] ?? null;
        unset($userData['seller_profile']);

        // Preparar dados do usuário
        $userAttributes = [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => $userData['role'],
            'email_verified_at' => $userData['email_verified_at'] ? now() : null,
        ];

        $userAttributes['password'] = Hash::make($userData['password']);

        // Criar ou atualizar usuário
        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            $userAttributes
        );

        echo "├── Usuário: {$user->email} ({$user->role})\n";

        // Se for seller, criar/atualizar perfil
        if ($user->role === 'seller' && $sellerData) {
            $profileAttributes = [
                'user_id' => $user->id,
                'document_type' => $sellerData['document_type'],
                'document_number' => $sellerData['document_number'],
                'company_name' => $sellerData['company_name'],
                'phone' => $sellerData['phone'],
                'address' => $sellerData['address'],
                'city' => $sellerData['city'],
                'state' => $sellerData['state'],
                'postal_code' => $sellerData['postal_code'],
                'bank_name' => $sellerData['bank_name'],
                'bank_account' => $sellerData['bank_account'],
                'status' => $sellerData['status'],
                'commission_rate' => $sellerData['commission_rate'],
                'product_limit' => $sellerData['product_limit'],
                'approved_at' => $sellerData['approved_at'] ? now() : null,
                'submitted_at' => $sellerData['submitted_at'] ? now() : null,
            ];

            SellerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileAttributes
            );

            echo "    └── Perfil de seller criado/atualizado\n";
        }
    }

    /**
     * Verificar se todos os usuários essenciais existem
     */
    public static function verifyEssentialUsers(): bool
    {
        $seeder = new self();
        $requiredEmails = collect($seeder->essentialUsers)->pluck('email');
        $existingEmails = User::whereIn('email', $requiredEmails)->pluck('email');
        
        return $requiredEmails->count() === $existingEmails->count();
    }

    /**
     * Obter credenciais dos usuários essenciais para testes
     */
    public static function getTestCredentials(): array
    {
        $seeder = new self();
        return collect($seeder->essentialUsers)->map(function ($user) {
            return [
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'role' => $user['role'],
            ];
        })->toArray();
    }
}