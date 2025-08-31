<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $store = $user->store;
        
        if (!$store) {
            return redirect()->route('seller.onboarding.index')
                ->with('warning', 'Complete seu cadastro de loja primeiro.');
        }
        
        return view('seller.profile.edit', compact('store'));
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        $store = $user->store;
        
        if (!$store) {
            return redirect()->route('seller.onboarding.index');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('stores')->ignore($store->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'cnpj' => ['required', 'string', 'size:18'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'size:2'],
            'zip_code' => ['required', 'string', 'size:9'],
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,webp'],
            'banner' => ['nullable', 'image', 'max:5120', 'mimes:jpeg,png,jpg,webp'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'business_hours' => ['nullable', 'json'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'min_order_value' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'estimated_delivery_time' => ['nullable', 'string', 'max:50'],
            'payment_methods' => ['nullable', 'array'],
            'payment_methods.*' => ['string', 'in:credit_card,debit_card,pix,boleto,cash'],
            'accepts_pickup' => ['boolean'],
            'accepts_delivery' => ['boolean'],
        ]);
        
        // Processar logo se enviado
        if ($request->hasFile('logo')) {
            // Deletar logo anterior se existir
            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }
            
            $logoPath = $request->file('logo')->store('stores/logos', 'public');
            $validated['logo'] = $logoPath;
        }
        
        // Processar banner se enviado
        if ($request->hasFile('banner')) {
            // Deletar banner anterior se existir
            if ($store->banner) {
                Storage::disk('public')->delete($store->banner);
            }
            
            $bannerPath = $request->file('banner')->store('stores/banners', 'public');
            $validated['banner'] = $bannerPath;
        }
        
        // Converter payment_methods para JSON se enviado
        if (isset($validated['payment_methods'])) {
            $validated['payment_methods'] = json_encode($validated['payment_methods']);
        }
        
        // Atualizar configurações da loja
        $store->update($validated);
        
        // Atualizar informações do usuário também
        $user->update([
            'name' => $validated['name'],
        ]);
        
        return redirect()->route('seller.profile.edit')
            ->with('success', 'Perfil da loja atualizado com sucesso!');
    }
    
    public function updateBankAccount(Request $request)
    {
        $store = auth()->user()->store;
        
        if (!$store) {
            return redirect()->route('seller.onboarding.index');
        }
        
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_code' => ['required', 'string', 'max:10'],
            'agency' => ['required', 'string', 'max:10'],
            'account_number' => ['required', 'string', 'max:20'],
            'account_type' => ['required', 'in:checking,savings'],
            'account_holder_name' => ['required', 'string', 'max:255'],
            'account_holder_document' => ['required', 'string', 'size:14'],
        ]);
        
        $store->update([
            'bank_account' => json_encode($validated)
        ]);
        
        return redirect()->route('seller.profile.edit')
            ->with('success', 'Dados bancários atualizados com sucesso!');
    }
    
    public function updateNotifications(Request $request)
    {
        $store = auth()->user()->store;
        
        if (!$store) {
            return redirect()->route('seller.onboarding.index');
        }
        
        $validated = $request->validate([
            'notify_new_order' => ['boolean'],
            'notify_order_cancelled' => ['boolean'],
            'notify_low_stock' => ['boolean'],
            'notify_new_review' => ['boolean'],
            'notify_payment_received' => ['boolean'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        
        $store->update([
            'notification_settings' => json_encode($validated)
        ]);
        
        return redirect()->route('seller.profile.edit')
            ->with('success', 'Configurações de notificação atualizadas!');
    }
    
    public function updateSeo(Request $request)
    {
        $store = auth()->user()->store;
        
        if (!$store) {
            return redirect()->route('seller.onboarding.index');
        }
        
        $validated = $request->validate([
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
        ]);
        
        $store->update([
            'seo_settings' => json_encode($validated)
        ]);
        
        return redirect()->route('seller.profile.edit')
            ->with('success', 'Configurações de SEO atualizadas!');
    }
    
    public function deactivate(Request $request)
    {
        $store = auth()->user()->store;
        
        if (!$store) {
            return redirect()->route('seller.dashboard');
        }
        
        $request->validate([
            'confirm' => ['required', 'in:DESATIVAR'],
            'reason' => ['required', 'string', 'max:500'],
        ]);
        
        $store->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);
        
        // Desativar todos os produtos
        $store->products()->update(['is_active' => false]);
        
        auth()->logout();
        
        return redirect()->route('home')
            ->with('info', 'Sua loja foi desativada. Entre em contato conosco se desejar reativá-la.');
    }
}