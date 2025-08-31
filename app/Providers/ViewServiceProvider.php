<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartilhar configurações básicas com todas as views
        View::composer('*', function ($view) {
            $user = auth()->user();
            $role = $user ? ($user->role ?? 'customer') : 'guest';
            
            $layoutData = [
                'user_role' => $role,
                'is_authenticated' => auth()->check(),
                'user_name' => $user ? $user->name : null,
                'user_avatar' => $user ? ($user->avatar_url ?? null) : null,
                'cart_count' => 0,
                'notification_count' => 0,
                'sidebar_visible' => in_array($role, ['admin', 'seller']),
                'permissions' => $this->getUserPermissions($role),
                'app_config' => [
                    'name' => config('app.name'),
                    'domain' => config('app.domain'),
                    'debug' => config('app.debug'),
                ]
            ];
            
            $view->with([
                'siteName' => config('app.name'),
                'siteTagline' => 'Marketplace Comunitário',
                'siteDescription' => 'Marketplace Comunitário',
                'siteLogo' => null,
                'siteFavicon' => null,
                'layoutSettings' => null,
                'layoutData' => $layoutData,
            ]);
        });
    }
    
    private function getUserPermissions($role)
    {
        $permissions = [
            'guest' => [],
            'customer' => ['browse_products', 'make_purchases', 'view_orders'],
            'seller' => ['browse_products', 'make_purchases', 'view_orders', 'manage_products', 'view_sales', 'manage_store'],
            'admin' => ['*']
        ];
        
        return $permissions[$role] ?? [];
    }
}