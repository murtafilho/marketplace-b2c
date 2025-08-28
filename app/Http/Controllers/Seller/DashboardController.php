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
            return redirect()->route('seller.onboarding');
        }

        // Redirecionar baseado no status
        switch ($seller->status) {
            case 'pending':
            case 'pending_approval':
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
        $stats = [
            'products_total' => $seller->products()->count(),
            'products_active' => $seller->products()->where('status', 'active')->count(),
            'orders_total' => 0, // TODO: implementar quando tiver orders
            'sales_total' => 0, // TODO: implementar
        ];

        return view('seller.dashboard', compact('seller', 'stats'));
    }
}