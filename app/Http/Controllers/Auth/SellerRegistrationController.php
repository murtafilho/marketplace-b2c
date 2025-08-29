<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class SellerRegistrationController extends Controller
{
    /**
     * Display the seller registration form.
     */
    public function create()
    {
        // Se usuário já está logado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Se já tem loja, redireciona para dashboard
            if ($user->sellerProfile) {
                return redirect()->route('seller.dashboard')
                    ->with('info', 'Você já possui uma loja cadastrada.');
            }
            
            // Se não tem loja, mostra formulário simplificado (só dados da loja)
            return view('auth.seller-registration', [
                'userLoggedIn' => true,
                'user' => $user
            ]);
        }
        
        // Usuário não logado - mostra formulário completo
        return view('auth.seller-registration', [
            'userLoggedIn' => false
        ]);
    }

    /**
     * Handle seller registration request.
     */
    public function store(Request $request)
    {
        // Se usuário está logado, validação simplificada
        if (Auth::check()) {
            return $this->createStoreForExistingUser($request);
        }
        
        // Usuário não logado - cadastro completo
        return $this->createUserAndStore($request);
    }

    /**
     * Create store for existing user
     */
    private function createStoreForExistingUser(Request $request)
    {
        $user = Auth::user();
        
        // Verificar se já tem loja
        if ($user->sellerProfile) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Você já possui uma loja cadastrada.');
        }
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string|max:500',
            'accept_terms' => 'required|accepted',
        ]);
        
        DB::transaction(function () use ($user, $request) {
            // Atualizar role se necessário
            if ($user->role === 'customer') {
                $user->update(['role' => 'seller']);
            }
            
            // Criar perfil de vendedor
            $profileData = [
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'status' => 'pending',
                'commission_rate' => 10.00,
                'product_limit' => 50,
            ];
            
            if ($request->filled('company_description')) {
                $profileData['description'] = $request->company_description;
            }
            
            SellerProfile::create($profileData);
        });
        
        return redirect()->route('seller.onboarding.index')
            ->with('success', 'Loja criada com sucesso! Complete seu cadastro para começar a vender.');
    }

    /**
     * Create new user and store
     */
    private function createUserAndStore(Request $request)
    {
        $request->validate([
            // Dados do usuário
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Dados da loja
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string|max:500',
            'accept_terms' => 'required|accepted',
        ]);

        DB::transaction(function () use ($request) {
            // Criar usuário
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'seller',
                'email_verified_at' => now(), // Auto-verifica para simplificar
            ]);

            event(new Registered($user));

            // Criar perfil de vendedor
            $profileData = [
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'status' => 'pending',
                'commission_rate' => 10.00,
                'product_limit' => 50,
            ];
            
            if ($request->filled('company_description')) {
                $profileData['description'] = $request->company_description;
            }
            
            SellerProfile::create($profileData);

            // Fazer login automático
            Auth::login($user);
        });

        return redirect()->route('seller.onboarding.index')
            ->with('success', 'Conta e loja criadas com sucesso! Complete seu cadastro para começar a vender.');
    }
}