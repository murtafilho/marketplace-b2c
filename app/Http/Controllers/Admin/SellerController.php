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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $query = SellerProfile::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('company_name', 'like', "%{$search}%");
        }

        $sellers = $query->orderBy('created_at', 'desc')->paginate(15);

        $statusCounts = [
            'all' => SellerProfile::count(),
            'pending' => SellerProfile::where('status', 'pending')->count(),
            'pending_approval' => SellerProfile::where('status', 'pending_approval')->count(),
            'approved' => SellerProfile::where('status', 'approved')->count(),
            'rejected' => SellerProfile::where('status', 'rejected')->count(),
        ];

        return view('admin.sellers.index', compact('sellers', 'statusCounts'));
    }

    public function show(SellerProfile $seller)
    {
        $seller->load('user');
        return view('admin.sellers.show', compact('seller'));
    }

    public function approve(SellerProfile $seller)
    {
        if ($seller->status !== 'pending_approval') {
            return back()->with('error', 'Vendedor não está pendente de aprovação.');
        }

        $seller->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Vendedor aprovado com sucesso!');
    }

    public function reject(Request $request, SellerProfile $seller)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        if ($seller->status !== 'pending_approval') {
            return back()->with('error', 'Vendedor não está pendente de aprovação.');
        }

        $seller->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Vendedor rejeitado.');
    }

    public function updateCommission(Request $request, SellerProfile $seller)
    {
        $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:50'],
        ]);

        $seller->update([
            'commission_rate' => $request->commission_rate,
        ]);

        return back()->with('success', 'Taxa de comissão atualizada com sucesso!');
    }

    public function suspend(SellerProfile $seller)
    {
        $seller->update(['status' => 'suspended']);
        $seller->user->update(['is_active' => false]);

        return back()->with('success', 'Vendedor suspenso com sucesso!');
    }

    public function activate(SellerProfile $seller)
    {
        $seller->update(['status' => 'approved']);
        $seller->user->update(['is_active' => true]);

        return back()->with('success', 'Vendedor reativado com sucesso!');
    }

    public function downloadDocument(SellerProfile $seller, string $type)
    {
        $filePath = match($type) {
            'address_proof' => $seller->address_proof_path,
            'identity_proof' => $seller->identity_proof_path,
            default => null,
        };

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Documento não encontrado.');
        }

        $fileName = match($type) {
            'address_proof' => "comprovante_endereco_{$seller->id}.pdf",
            'identity_proof' => "documento_identidade_{$seller->id}.pdf",
        };

        return Storage::disk('public')->download($filePath, $fileName);
    }
}