<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class InjectLayoutData
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $role = $user ? ($user->role ?? 'customer') : 'guest';
        
        $layoutData = [
            'user_role' => $role,
            'is_authenticated' => Auth::check(),
            'user_name' => $user ? $user->name : null,
            'user_avatar' => $user ? $user->avatar_url : null,
            'cart_count' => $this->getCartCount(),
            'notification_count' => $this->getNotificationCount(),
            'sidebar_visible' => in_array($role, ['admin', 'seller']),
            'permissions' => $this->getUserPermissions($role),
            'app_config' => [
                'name' => config('app.name'),
                'domain' => config('app.domain'),
                'debug' => config('app.debug'),
            ]
        ];
        
        View::share('layoutData', $layoutData);
        
        return $next($request);
    }
    
    private function getCartCount()
    {
        if (!Auth::check()) {
            return 0;
        }
        
        // Implement cart count logic here
        // For now, return a placeholder
        return 0;
    }
    
    private function getNotificationCount()
    {
        if (!Auth::check()) {
            return 0;
        }
        
        // Implement notification count logic here
        // For now, return a placeholder
        return 0;
    }
    
    private function getUserPermissions($role)
    {
        $permissions = [
            'guest' => [],
            'customer' => ['browse_products', 'make_purchases', 'view_orders'],
            'seller' => ['browse_products', 'make_purchases', 'view_orders', 'manage_products', 'view_sales', 'manage_store'],
            'admin' => ['*'] // All permissions
        ];
        
        return $permissions[$role] ?? [];
    }
}