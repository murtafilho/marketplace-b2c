<?php
/**
 * Arquivo: app/Http/Controllers/Seller/DashboardController.php
 * Descrição: Dashboard do vendedor
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Middleware aplicado via routes/web.php

    public function index()
    {
        $seller = auth()->user()->sellerProfile;

        if (!$seller) {
            return redirect()->route('seller.onboarding.index');
        }

        // Redirecionar baseado no status
        switch ($seller->status) {
            case 'pending':
                return $this->pendingView($seller);
            
            case 'rejected':
                return $this->rejectedView($seller);
            
            case 'approved':
                return $this->dashboardView($seller);
            
            default:
                return redirect('/');
        }
    }

    private function pendingView($seller)
    {
        return view('seller.pending', compact('seller'));
    }

    private function rejectedView($seller)
    {
        return view('seller.rejected', compact('seller'));
    }

    private function dashboardView($seller)
    {
        // Estatísticas dos produtos
        $products = $seller->products();
        $stats = [
            'products_total' => $products->count(),
            'products_active' => $products->where('status', 'active')->count(),
            'products_draft' => $products->where('status', 'draft')->count(),
            'products_out_of_stock' => $products->where('stock_quantity', 0)->count(),
            'orders_total' => 0, // TODO: implementar quando tiver orders
            'orders_pending' => 0,
            'sales_total' => 0, // TODO: implementar
            'revenue_month' => 0,
            'views_total' => $products->sum('views_count'),
            'average_rating' => 0,
            'commission_rate' => $seller->commission_rate ?? 10,
            'account_status' => $seller->status,
            'member_since' => $seller->created_at,
            'last_sale' => null,
        ];

        // Produtos recentes
        $recentProducts = $seller->products()
            ->with('category', 'images')
            ->latest()
            ->take(5)
            ->get();

        // Produtos com baixo estoque (menos de 5 unidades)
        $lowStockProducts = $seller->products()
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

        // Produtos mais visualizados
        $topProducts = $seller->products()
            ->where('views_count', '>', 0)
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        // Alertas e notificações
        $alerts = [];
        
        if ($stats['products_out_of_stock'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Você tem {$stats['products_out_of_stock']} produto(s) sem estoque",
                'action' => route('seller.products.index', ['filter' => 'out_of_stock'])
            ];
        }

        if ($stats['products_draft'] > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "Você tem {$stats['products_draft']} produto(s) em rascunho",
                'action' => route('seller.products.index', ['filter' => 'draft'])
            ];
        }

        if (!$seller->mp_connected) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Conecte sua conta do Mercado Pago para receber pagamentos',
                'action' => '#'
            ];
        }

        return view('seller.dashboard', compact('seller', 'stats', 'recentProducts', 'lowStockProducts', 'topProducts', 'alerts'));
    }

    /**
     * Transforma um usuário logado em vendedor
     */
    public function becomeSeller(Request $request)
    {
        $user = auth()->user();
        
        // Verificar se já tem perfil de vendedor
        if ($user->sellerProfile) {
            return redirect()->route('seller.dashboard')
                ->with('info', 'Você já possui um perfil de vendedor.');
        }
        
        // Criar perfil de vendedor
        $user->sellerProfile()->create([
            'company_name' => $user->name,
            'status' => 'pending',
            'commission_rate' => config('marketplace.default_commission', 10.0),
        ]);
        
        // Atualizar o role do usuário se não for admin
        if ($user->role !== 'admin') {
            $user->update(['role' => 'seller']);
        }
        
        return redirect()->route('seller.onboarding.index')
            ->with('success', 'Perfil de vendedor criado! Complete seu cadastro para começar a vender.');
    }
}