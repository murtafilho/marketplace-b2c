<?php
/**
 * Arquivo: app/Models/SellerProfile.php
 * Descrição: Modelo do perfil de vendedores com dados adicionais
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'company_name',
        'address_proof_path',
        'identity_proof_path',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'bank_name',
        'bank_agency',
        'bank_account',
        'status',
        'rejection_reason',
        'commission_rate',
        'product_limit',
        'mp_access_token',
        'mp_user_id',
        'mp_connected',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'mp_connected' => 'boolean',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'seller_id', 'user_id');
    }

    public function subOrders()
    {
        return $this->hasMany(SubOrder::class, 'seller_id', 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canSellProducts(): bool
    {
        return $this->isApproved() && $this->mp_connected;
    }
}
