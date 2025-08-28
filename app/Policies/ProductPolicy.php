<?php
/**
 * Arquivo: app/Policies/ProductPolicy.php
 * Descrição: Policy para autorização de produtos
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'seller' || $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        // Seller can only view their own products
        if ($user->role === 'seller') {
            return $product->seller_id === $user->sellerProfile?->id;
        }

        // Admin can view all products
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only approved sellers can create products
        if ($user->role === 'seller') {
            return $user->sellerProfile?->status === 'approved';
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        // Seller can only update their own products
        if ($user->role === 'seller') {
            return $product->seller_id === $user->sellerProfile?->id;
        }

        // Admin can update all products
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // Seller can only delete their own products
        if ($user->role === 'seller') {
            return $product->seller_id === $user->sellerProfile?->id;
        }

        // Admin can delete all products
        return $user->role === 'admin';
    }
}