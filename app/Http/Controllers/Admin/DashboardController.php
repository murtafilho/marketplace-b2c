<?php
/**
 * Arquivo: app/Http/Controllers/Admin/DashboardController.php
 * Descrição: Controller do dashboard administrativo
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    // Middleware aplicado via routes/web.php

    public function index()
    {
        // Estatísticas básicas
        $usersTotal = User::count();
        $sellersApproved = SellerProfile::where('status', 'approved')->count();
        $sellersPending = SellerProfile::whereIn('status', ['pending', 'pending'])->count();
        $productsActive = Product::where('status', 'active')->count();
        
        // Calcular taxas
        $sellersTotal = SellerProfile::count();
        $sellersApprovedRate = $sellersTotal > 0 ? round(($sellersApproved / $sellersTotal) * 100, 1) : 0;
        
        // Dados para o mês atual
        $usersNewThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $stats = [
            // Estatísticas principais
            'users_total' => $usersTotal,
            'users_new_this_month' => $usersNewThisMonth,
            'sellers_approved' => $sellersApproved,
            'sellers_approved_rate' => $sellersApprovedRate,
            'sellers_pending' => $sellersPending,
            'products_active' => $productsActive,
            'categories_count' => Category::where('is_active', true)->count(),
            
            // Estatísticas adicionais
            'revenue_total' => 0, // TODO: Implementar quando tiver transactions
            'revenue_growth' => 0, // TODO: Calcular crescimento
            'orders_total' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'commissions_total' => 0, // TODO: Somar comissões das transações
            'commission_rate' => 10.0, // Taxa padrão por enquanto
        ];

        // Vendedores pendentes
        $recent_sellers = SellerProfile::with('user')
            ->whereIn('status', ['pending', 'pending'])
            ->latest('submitted_at')
            ->limit(8)
            ->get();

        // Pedidos recentes (mock data por enquanto)
        $recent_orders = collect(); // Order::with('user')->latest()->limit(10)->get();
        
        // Atividades recentes (mock data)
        $recent_activities = $this->getRecentActivities();

        return view('admin.dashboard', compact('stats', 'recent_sellers', 'recent_orders', 'recent_activities'));
    }
    
    /**
     * Gerar atividades recentes simuladas
     */
    private function getRecentActivities()
    {
        $activities = collect();
        
        // Últimos vendedores aprovados
        $approvedSellers = SellerProfile::with('user')
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->latest('approved_at')
            ->limit(5)
            ->get();
            
        foreach ($approvedSellers as $seller) {
            $activities->push([
                'type' => 'seller_approved',
                'message' => "Vendedor {$seller->user->name} foi aprovado",
                'created_at' => $seller->approved_at ?? $seller->updated_at,
            ]);
        }
        
        // Últimos vendedores registrados
        $newSellers = SellerProfile::with('user')
            ->latest('created_at')
            ->limit(3)
            ->get();
            
        foreach ($newSellers as $seller) {
            $activities->push([
                'type' => 'seller_registered',
                'message' => "Novo vendedor cadastrado: {$seller->user->name}",
                'created_at' => $seller->created_at,
            ]);
        }
        
        // Últimos produtos criados
        $newProducts = Product::with(['seller.user'])
            ->where('status', '!=', 'draft')
            ->latest('created_at')
            ->limit(3)
            ->get();
            
        foreach ($newProducts as $product) {
            $sellerName = $product->seller->user->name ?? 'Vendedor';
            $activities->push([
                'type' => 'product_created',
                'message' => "Produto '{$product->name}' criado por {$sellerName}",
                'created_at' => $product->created_at,
            ]);
        }
        
        return $activities->sortByDesc('created_at')->take(10);
    }
}