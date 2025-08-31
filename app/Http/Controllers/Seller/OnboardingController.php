<?php
/**
 * Arquivo: app/Http/Controllers/Seller/OnboardingController.php
 * Descrição: Controller para onboarding de vendedores
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\SafeUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    protected $uploadService;

    public function __construct(SafeUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

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

        // Clean document number for validation
        $cleanDocumentNumber = preg_replace('/\D/', '', $request->document_number);
        $request->merge(['document_number_clean' => $cleanDocumentNumber]);

        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:cpf,cnpj'],
            'document_number' => ['required', 'string'],
            'document_number_clean' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->document_type === 'cpf') {
                        if (strlen($value) !== 11) {
                            $fail('O CPF deve conter 11 dígitos.');
                            return;
                        }
                        if (!$this->validateCPF($value)) {
                            $fail('O CPF informado é inválido.');
                        }
                    } elseif ($request->document_type === 'cnpj') {
                        if (strlen($value) !== 14) {
                            $fail('O CNPJ deve conter 14 dígitos.');
                            return;
                        }
                        if (!$this->validateCNPJ($value)) {
                            $fail('O CNPJ informado é inválido.');
                        }
                    }
                },
                Rule::unique('seller_profiles', 'document_number')->ignore($profile->id),
            ],
            'phone' => ['required', 'string', 'min:14', 'max:15'], // (11) 99999-9999
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'size:2'],
            'postal_code' => ['required', 'string', 'size:9'], // 00000-000
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account' => ['required', 'string', 'max:50'],
            'address_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'identity_proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'document_number.required' => 'O número do documento é obrigatório.',
            'phone.min' => 'O telefone deve estar no formato (11) 99999-9999.',
            'phone.max' => 'O telefone deve estar no formato (11) 99999-9999.',
            'postal_code.size' => 'O CEP deve estar no formato 00000-000.',
            'state.size' => 'Selecione um estado válido.',
            'address_proof.required' => 'O comprovante de endereço é obrigatório.',
            'address_proof.mimes' => 'O comprovante de endereço deve ser um arquivo PDF, JPG, JPEG ou PNG.',
            'address_proof.max' => 'O comprovante de endereço deve ter no máximo 2MB.',
            'identity_proof.required' => 'O documento de identidade é obrigatório.',
            'identity_proof.mimes' => 'O documento de identidade deve ser um arquivo PDF, JPG, JPEG ou PNG.',
            'identity_proof.max' => 'O documento de identidade deve ter no máximo 2MB.',
        ]);

        // Upload files using SafeUploadService (opcional)
        $addressProofPath = null;
        $identityProofPath = null;
        
        try {
            if ($request->hasFile('address_proof')) {
                $addressProofResult = $this->uploadService->uploadDocument(
                    $request->file('address_proof'), 
                    'seller-documents'
                );
                $addressProofPath = $addressProofResult['file_path'];
            }
            
            if ($request->hasFile('identity_proof')) {
                $identityProofResult = $this->uploadService->uploadDocument(
                    $request->file('identity_proof'), 
                    'seller-documents'
                );
                $identityProofPath = $identityProofResult['file_path'];
            }
            
            \Log::info('Documents upload processed', [
                'address_proof' => $addressProofPath,
                'identity_proof' => $identityProofPath
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro no upload de documentos: ' . $e->getMessage());
            return redirect()->back()->withErrors([
                'upload' => 'Erro no upload dos arquivos: ' . $e->getMessage()
            ])->withInput();
        }

        // Update seller profile - arquivos são opcionais agora
        $updateData = [
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
            'status' => 'pending_approval',
            'submitted_at' => now(),
        ];
        
        // Adicionar paths apenas se os arquivos foram enviados
        if ($addressProofPath) {
            $updateData['address_proof_path'] = $addressProofPath;
        }
        
        if ($identityProofPath) {
            $updateData['identity_proof_path'] = $identityProofPath;
        }
        
        $profile->update($updateData);

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

    private function validateCPF($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }
            $remainder = $sum % 11;
            $digit = $remainder < 2 ? 0 : 11 - $remainder;
            if ($cpf[$t] != $digit) {
                return false;
            }
        }
        
        return true;
    }

    private function validateCNPJ($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        
        if (strlen($cnpj) != 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }
        
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights1[$i];
        }
        $remainder = $sum % 11;
        $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
        
        if ($cnpj[12] != $digit1) {
            return false;
        }
        
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights2[$i];
        }
        $remainder = $sum % 11;
        $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
        
        return $cnpj[13] == $digit2;
    }

}