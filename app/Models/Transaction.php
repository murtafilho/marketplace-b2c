<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'sub_order_id',
        'seller_id',
        'mp_payment_id',
        'type',
        'amount',
        'commission_rate',
        'commission_amount',
        'seller_amount',
        'status',
        'processed_at',
        'mp_collector_id',
        'mp_response',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'commission_amount' => 'decimal:2',
        'seller_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'mp_response' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function subOrder(): BelongsTo
    {
        return $this->belongsTo(SubOrder::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Calcular comissão e valor do seller automaticamente
            if ($transaction->amount && $transaction->commission_rate) {
                $transaction->commission_amount = $transaction->amount * ($transaction->commission_rate / 100);
                $transaction->seller_amount = $transaction->amount - $transaction->commission_amount;
            }
        });

        static::updating(function ($transaction) {
            // Recalcular se amount ou commission_rate mudaram
            if ($transaction->isDirty(['amount', 'commission_rate'])) {
                $transaction->commission_amount = $transaction->amount * ($transaction->commission_rate / 100);
                $transaction->seller_amount = $transaction->amount - $transaction->commission_amount;
            }
        });
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function markAsCompleted(array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);
    }

    public function markAsFailed(string $reason = null, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);
    }

    public function getCommissionPercentageAttribute(): float
    {
        return $this->commission_rate;
    }

    public function getSellerReceivesAttribute(): float
    {
        return $this->seller_amount;
    }

    public function getMarketplaceReceivesAttribute(): float
    {
        return $this->commission_amount;
    }
}