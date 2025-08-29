<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SellerManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = SellerProfile::with(['user'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                })->orWhere('company_name', 'LIKE', "%{$search}%");
            });

        $sellers = $query->latest()->paginate(15);
        
        $stats = [
            'total' => SellerProfile::count(),
            'pending' => SellerProfile::where('status', 'pending_approval')->count(),
            'approved' => SellerProfile::where('status', 'approved')->count(),
            'rejected' => SellerProfile::where('status', 'rejected')->count(),
            'suspended' => SellerProfile::where('status', 'suspended')->count(),
        ];

        return view('admin.sellers.index', compact('sellers', 'stats'));
    }

    public function show(SellerProfile $seller)
    {
        $seller->load('user');
        return view('admin.sellers.show', compact('seller'));
    }

    public function approve(SellerProfile $seller)
    {
        if ($seller->status !== 'pending_approval') {
            return redirect()->back()->with('error', 'Vendedor não está pendente de aprovação.');
        }

        $seller->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Vendedor aprovado com sucesso!');
    }

    public function reject(SellerProfile $seller, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        if ($seller->status !== 'pending_approval') {
            return redirect()->back()->with('error', 'Vendedor não está pendente de aprovação.');
        }

        $seller->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Vendedor rejeitado.');
    }

    public function suspend(SellerProfile $seller)
    {
        if ($seller->status !== 'approved') {
            return redirect()->back()->with('error', 'Apenas vendedores aprovados podem ser suspensos.');
        }

        $seller->update(['status' => 'suspended']);
        $seller->user->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Vendedor suspenso com sucesso!');
    }

    public function updateCommission(SellerProfile $seller, Request $request)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100'
        ]);

        $seller->update([
            'commission_rate' => $request->commission_rate
        ]);

        return redirect()->back()->with('success', 'Taxa de comissão atualizada!');
    }
}