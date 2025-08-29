<?php
/**
 * Arquivo: database/seeders/ProtectedUsersSeeder.php
 * Descrição: Seeder para usuários SEMPRE PRESERVADOS do sistema
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 * 
 * IMPORTANTE: Este seeder garante que os usuários essenciais
 * sempre existam, independente de outras operações de seed.
 */

namespace Database\Seeders;

use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProtectedUsersSeeder extends Seeder
{
    /**
     * Retorna os dados dos usuários que NUNCA devem ser removidos
     */
    private function getProtectedUsersData(): array
    {
        return [
            [
                'name' => 'Administrador Sistema',
                'email' => 'admin@marketplace.com',
                'password' => 'admin123',
                'role' => 'admin',
                'seller_profile' => null
            ],
            [
                'name' => 'Tech Store Brasil',
                'email' => 'tech@marketplace.com', 
                'password' => 'seller123',
                'role' => 'seller',
                'seller_profile' => [
                    'company_name' => 'Tech Store Brasil',
                    'document_type' => 'CNPJ',
                    'document_number' => '12.345.678/0001-90',
                    'status' => 'approved',
                    'commission_rate' => 8.00,
                    'approved_at' => now(),
                    'submitted_at' => now()->subDays(30),
                ]
            ],
            [
                'name' => 'Cliente Teste',
                'email' => 'cliente@marketplace.com',
                'password' => 'cliente123', 
                'role' => 'customer',
                'seller_profile' => null
            ]
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔒 CRIANDO/ATUALIZANDO USUÁRIOS PROTEGIDOS...\n";
        echo str_repeat("-", 50) . "\n";

        foreach ($this->getProtectedUsersData() as $userData) {
            $this->ensureUserExists($userData);
        }

        echo "\n✅ Todos os usuários protegidos foram verificados e estão disponíveis!\n";
    }

    /**
     * Garante que um usuário existe (cria se não existir, atualiza se necessário)
     */
    private function ensureUserExists(array $userData): void
    {
        $user = User::where('email', $userData['email'])->first();
        
        if (!$user) {
            // Criar usuário
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role' => $userData['role'],
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
            
            echo "  ✅ Usuário criado: {$userData['name']} ({$userData['email']})\n";
        } else {
            // Atualizar dados se necessário (mas preservar email e senha)
            $user->update([
                'name' => $userData['name'],
                'role' => $userData['role'],
                'is_active' => true,
                'email_verified_at' => now(), // Forçar verificação sempre
            ]);
            
            echo "  🔄 Usuário atualizado: {$userData['name']} ({$userData['email']})\n";
        }

        // Criar perfil de vendedor se necessário
        if ($userData['seller_profile'] && $userData['role'] === 'seller') {
            $this->ensureSellerProfileExists($user, $userData['seller_profile']);
        }
    }

    /**
     * Garante que o perfil de vendedor existe
     */
    private function ensureSellerProfileExists(User $user, array $profileData): void
    {
        $profile = $user->sellerProfile;
        
        if (!$profile) {
            // Obter categoria padrão ou criar uma
            $defaultCategory = Category::first();
            if (!$defaultCategory) {
                $defaultCategory = Category::create([
                    'name' => 'Eletrônicos',
                    'slug' => 'eletronicos',
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            $profile = SellerProfile::create(array_merge($profileData, [
                'user_id' => $user->id,
                'phone' => '(11) 99999-8888',
                'address' => 'Rua das Empresas, 123',
                'city' => 'São Paulo',
                'state' => 'SP', 
                'postal_code' => '01234-567',
                'product_limit' => 500, // Limite maior para usuário protegido
                'mp_connected' => false,
            ]));
            
            echo "    ➕ Perfil de vendedor criado para: {$user->name}\n";
        } else {
            // Atualizar dados importantes
            $profile->update([
                'status' => $profileData['status'],
                'commission_rate' => $profileData['commission_rate'],
                'approved_at' => $profileData['approved_at'] ?? $profile->approved_at,
                'submitted_at' => $profileData['submitted_at'] ?? $profile->submitted_at,
            ]);
            
            echo "    🔄 Perfil de vendedor atualizado para: {$user->name}\n";
        }
    }

    /**
     * Método para verificar se todos os usuários protegidos existem
     */
    public static function verifyProtectedUsers(): bool
    {
        $instance = new self();
        $missingUsers = [];
        
        foreach ($instance->getProtectedUsersData() as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                $missingUsers[] = $userData['email'];
            }
        }
        
        if (!empty($missingUsers)) {
            echo "⚠️ USUÁRIOS PROTEGIDOS FALTANDO: " . implode(', ', $missingUsers) . "\n";
            return false;
        }
        
        return true;
    }

    /**
     * Retorna as credenciais dos usuários protegidos
     */
    public static function getProtectedCredentials(): array
    {
        $instance = new self();
        return collect($instance->getProtectedUsersData())->map(function ($user) {
            return [
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'role' => $user['role'],
            ];
        })->toArray();
    }
}