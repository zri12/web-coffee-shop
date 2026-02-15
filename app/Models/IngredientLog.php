<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'change_amount',
        'type',
        'reference_id',
        'note',
    ];

    protected $casts = [
        'change_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // Disable updated_at timestamp (we only need created_at for logs)
    const UPDATED_AT = null;

    /**
     * Get the ingredient
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Scope for order deductions
     */
    public function scopeOrderDeductions($query)
    {
        return $query->where('type', 'Order Deduct');
    }

    /**
     * Scope for restocks
     */
    public function scopeRestocks($query)
    {
        return $query->where('type', 'Restock');
    }

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted change amount with sign
     */
    public function getFormattedChangeAttribute(): string
    {
        $sign = $this->change_amount >= 0 ? '+' : '';
        return $sign . number_format($this->change_amount, 2);
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'Order Deduct' => 'bg-red-100 text-red-800',
            'Restock' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
