<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'table_number',
        'notes',
        'status',
        'total_amount',
        'order_type',
        'payment_method',
        'payment_status',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'table_number' => 'integer',
    ];

    /**
     * Get the user (cashier) who created this order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Boot - auto generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Calculate and update total
     */
    public function calculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->save();
    }

    /**
     * Status scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for today's orders
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for active orders (not cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getDisplayTotalAmountAttribute(): float
    {
        $storedTotal = (float) $this->total_amount;
        if ($storedTotal > 0) {
            return $storedTotal;
        }

        if (!$this->relationLoaded('items')) {
            return $storedTotal;
        }

        $fallback = $this->items->sum(function ($item) {
            if (!is_null($item->subtotal) && (float)$item->subtotal > 0) {
                return (float) $item->subtotal;
            }

            $unitPrice = (float) ($item->unit_price ?? $item->price ?? 0);
            $qty = max(1, (int) ($item->quantity ?? 1));
            return $unitPrice * $qty;
        });

        return (float) $fallback;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Delegate ingredient deduction to StockService (idempotent).
     */
    public function deductIngredients(bool $withTransaction = true): void
    {
        app(\App\Services\StockService::class)->deductStockForOrder($this, $withTransaction);
    }
}
