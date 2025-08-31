<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function financial(Request $request)
    {
        // Período padrão: último mês
        $startDate = $request->get('start_date', now()->subMonth()->startOfDay());
        $endDate = $request->get('end_date', now()->endOfDay());
        
        if (is_string($startDate)) $startDate = Carbon::parse($startDate);
        if (is_string($endDate)) $endDate = Carbon::parse($endDate);

        // Métricas principais
        $metrics = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'paid')->sum('total'),
            'total_commission' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('type', 'marketplace_commission')->sum('amount'),
            'pending_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'pending')->count(),
        ];

        // Vendas por dia
        $salesByDay = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top vendedores
        $topSellers = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('seller_profiles', 'products.seller_id', '=', 'seller_profiles.id')
            ->join('users', 'seller_profiles.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->select([
                'users.name as seller_name',
                'seller_profiles.company_name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.total_price) as total_sales')
            ])
            ->groupBy('users.id', 'users.name', 'seller_profiles.company_name')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get();

        // Métodos de pagamento
        $paymentMethods = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Status dos pedidos
        $orderStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('admin.reports.financial', compact(
            'metrics',
            'salesByDay',
            'topSellers',
            'paymentMethods', 
            'orderStatus',
            'startDate',
            'endDate'
        ));
    }

    public function sellers(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->startOfDay());
        $endDate = $request->get('end_date', now()->endOfDay());
        
        if (is_string($startDate)) $startDate = Carbon::parse($startDate);
        if (is_string($endDate)) $endDate = Carbon::parse($endDate);

        // Métricas de vendedores
        $sellerMetrics = [
            'total_sellers' => User::where('role', 'seller')->count(),
            'active_sellers' => DB::table('users')
                ->join('seller_profiles', 'users.id', '=', 'seller_profiles.user_id')
                ->where('seller_profiles.status', 'approved')
                ->count(),
            'pending_approval' => DB::table('seller_profiles')
                ->where('status', 'pending')
                ->count(),
            'rejected_sellers' => DB::table('seller_profiles')
                ->where('status', 'rejected')
                ->count(),
        ];

        // Vendedores com mais vendas
        $topPerformers = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('seller_profiles', 'products.seller_id', '=', 'seller_profiles.id')
            ->join('users', 'seller_profiles.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->select([
                'users.name',
                'seller_profiles.company_name',
                'seller_profiles.status',
                'seller_profiles.created_at as registered_at',
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.total_price) as total_revenue'),
                DB::raw('SUM(order_items.commission_amount) as commission_earned')
            ])
            ->groupBy(
                'users.id', 'users.name', 'seller_profiles.company_name', 
                'seller_profiles.status', 'seller_profiles.created_at'
            )
            ->orderByDesc('total_revenue')
            ->paginate(20);

        return view('admin.reports.sellers', compact(
            'sellerMetrics',
            'topPerformers',
            'startDate',
            'endDate'
        ));
    }

    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->startOfDay());
        $endDate = $request->get('end_date', now()->endOfDay());
        
        if (is_string($startDate)) $startDate = Carbon::parse($startDate);
        if (is_string($endDate)) $endDate = Carbon::parse($endDate);

        // Produtos mais vendidos
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->select([
                'products.name as product_name',
                'products.sku',
                'categories.name as category_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count')
            ])
            ->groupBy('products.id', 'products.name', 'products.sku', 'categories.name')
            ->orderByDesc('total_revenue')
            ->limit(50)
            ->get();

        // Vendas por categoria
        $categorySales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'paid')
            ->select([
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT order_items.id) as items_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT products.id) as unique_products')
            ])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('admin.reports.products', compact(
            'topProducts',
            'categorySales',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'financial');
        $format = $request->get('format', 'csv');
        
        // Para este exemplo, vamos retornar um CSV simples
        if ($type === 'financial') {
            $orders = Order::with(['items.product', 'user'])
                ->where('status', 'paid')
                ->when($request->get('start_date'), function($query, $date) {
                    return $query->where('created_at', '>=', Carbon::parse($date));
                })
                ->when($request->get('end_date'), function($query, $date) {
                    return $query->where('created_at', '<=', Carbon::parse($date));
                })
                ->get();

            $filename = 'relatorio_financeiro_' . now()->format('Y_m_d_H_i') . '.csv';
            
            return response()->streamDownload(function() use ($orders) {
                $handle = fopen('php://output', 'w');
                
                // Cabeçalho
                fputcsv($handle, [
                    'Pedido', 'Data', 'Cliente', 'Status', 'Total', 'Método Pagamento'
                ]);
                
                // Dados
                foreach ($orders as $order) {
                    fputcsv($handle, [
                        $order->order_number,
                        $order->created_at->format('d/m/Y H:i'),
                        $order->user->name,
                        $order->status,
                        'R$ ' . number_format($order->total, 2, ',', '.'),
                        $order->payment_method
                    ]);
                }
                
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        return redirect()->back()->with('error', 'Tipo de relatório não encontrado.');
    }
}