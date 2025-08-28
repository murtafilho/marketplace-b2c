<?php
/**
 * Arquivo: app/Http/Controllers/Seller/OnboardingController.php
 * Descrição: Controller para onboarding de vendedores
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        
        if (!$user->sellerProfile) {
            return redirect()->route('home')->with('error', 'Perfil de vendedor não encontrado.');
        }

        $profile = $user->sellerProfile;

        if ($profile->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.onboarding.index', compact('profile'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $profile = $user->sellerProfile;

        if (!$profile) {
            return redirect()->route('home')->with('error', 'Perfil de vendedor não encontrado.');
        }

        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:cpf,cnpj'],
            'document_number' => [
                'required',
                'string',
                Rule::when($request->document_type === 'cpf', ['size:14'], ['size:18']),
                Rule::unique('seller_profiles', 'document_number')->ignore($profile->id),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'size:2'],
            'postal_code' => ['required', 'string', 'size:9'],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:20'],
            'address_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'identity_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        // Upload files
        $addressProofPath = $request->file('address_proof')->store('seller-documents', 'public');
        $identityProofPath = $request->file('identity_proof')->store('seller-documents', 'public');

        // Update seller profile
        $profile->update([
            'company_name' => $request->company_name,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'address_proof_path' => $addressProofPath,
            'identity_proof_path' => $identityProofPath,
            'status' => 'pending_approval',
            'submitted_at' => now(),
        ]);

        // Update user phone if different
        if ($user->phone !== $request->phone) {
            $user->update(['phone' => $request->phone]);
        }

        return redirect()->route('seller.pending')
            ->with('success', 'Documentos enviados com sucesso! Aguarde a aprovação do administrador.');
    }

    public function pending()
    {
        $profile = auth()->user()->sellerProfile;
        
        if (!$profile) {
            return redirect()->route('home');
        }

        if ($profile->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.onboarding.pending', compact('profile'));
    }
}