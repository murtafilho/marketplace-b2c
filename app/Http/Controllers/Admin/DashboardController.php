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
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Middleware aplicado via routes/web.php

    public function index()
    {
        $stats = [
            'users_total' => User::count(),
            'users_sellers' => User::where('role', 'seller')->count(),
            'users_customers' => User::where('role', 'customer')->count(),
            'sellers_pending' => SellerProfile::where('status', 'pending_approval')->count(),
            'sellers_approved' => SellerProfile::where('status', 'approved')->count(),
            'products_total' => Product::count(),
            'products_active' => Product::where('status', 'active')->count(),
            'orders_total' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
        ];

        $recent_sellers = SellerProfile::with('user')
            ->where('status', 'pending_approval')
            ->latest()
            ->limit(5)
            ->get();

        $recent_orders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_sellers', 'recent_orders'));
    }
}