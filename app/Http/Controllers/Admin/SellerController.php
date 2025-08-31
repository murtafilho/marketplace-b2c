<?php
/**
 * Arquivo: app/Http/Controllers/Admin/SellerController.php
 * Descrição: Controller para gestão de vendedores pelo admin
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    // Middleware aplicado via routes/web.php

    /**
     * Lista todos os vendedores
     */
    public function index(Request $request)
    {
        $query = SellerProfile::with('user');

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Busca por nome ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sellers = $query->latest()->paginate(20);

        return view('admin.sellers.index', compact('sellers'));
    }

    /**
     * Exibe detalhes de um vendedor
     */
    public function show(SellerProfile $seller)
    {
        $seller->load('user', 'products');
        
        return view('admin.sellers.show', compact('seller'));
    }

    /**
     * Aprova um vendedor
     */
    public function approve(SellerProfile $seller)
    {
        if ($seller->status !== 'pending') {
            return back()->with('error', 'Apenas vendedores pendentes podem ser aprovados.');
        }

        $seller->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Vendedor aprovado com sucesso!');
    }

    /**
     * Rejeita um vendedor
     */
    public function reject(Request $request, SellerProfile $seller)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($seller->status !== 'pending') {
            return back()->with('error', 'Apenas vendedores pendentes podem ser rejeitados.');
        }

        $seller->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Vendedor rejeitado.');
    }

    /**
     * Suspende um vendedor
     */
    public function suspend(SellerProfile $seller)
    {
        if ($seller->status !== 'approved') {
            return back()->with('error', 'Apenas vendedores aprovados podem ser suspensos.');
        }

        $seller->update([
            'status' => 'suspended'
        ]);

        return back()->with('success', 'Vendedor suspenso.');
    }

    /**
     * Atualiza a taxa de comissão
     */
    public function updateCommission(Request $request, SellerProfile $seller)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:50'
        ]);

        $seller->update([
            'commission_rate' => $request->commission_rate
        ]);

        return back()->with('success', 'Taxa de comissão atualizada.');
    }
}