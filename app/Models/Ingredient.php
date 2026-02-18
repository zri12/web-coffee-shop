<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'stock',
        'minimum_stock',
        'status',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
    ];

    /**
     * Get product recipes using this ingredient
     */
    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }

    /**
     * Get ingredient logs
     */
    public function logs()
    {
        return $this->hasMany(IngredientLog::class);
    }

    /**
     * Scope for low stock ingredients
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= minimum_stock')->where('stock', '>', 0);
    }

    /**
     * Scope for out of stock ingredients
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Update status based on current stock
     */
    public function updateStatus(): void
    {
        if ($this->stock <= 0) {
            $this->status = 'Habis';
        } elseif ($this->stock <= $this->minimum_stock) {
            $this->status = 'Hampir Habis';
        } else {
            $this->status = 'Aman';
        }
        $this->save();
    }

    /**
     * Deduct stock and create log
     */
    public function deductStock(float $amount, ?int $orderId = null, ?string $note = null, ?int $productId = null, string $type = 'Order Deduct'): void
    {
        $previous = $this->stock;
        $this->stock -= $amount;
        $this->updateStatus();
        
        // Create log entry (backward compatible if new columns not migrated yet)
        $payload = [
            'change_amount' => -$amount,
            'type' => $type,
            'note' => $note,
        ];

        if (Schema::hasColumn('ingredient_logs', 'direction')) {
            $payload['direction'] = 'OUT';
        }
        if (Schema::hasColumn('ingredient_logs', 'order_id')) {
            $payload['order_id'] = $orderId;
        }
        if (Schema::hasColumn('ingredient_logs', 'product_id')) {
            $payload['product_id'] = $productId;
        }
        if (Schema::hasColumn('ingredient_logs', 'reference_id')) {
            $payload['reference_id'] = $orderId;
        }
        if (Schema::hasColumn('ingredient_logs', 'previous_stock')) {
            $payload['previous_stock'] = $previous;
        }
        if (Schema::hasColumn('ingredient_logs', 'new_stock')) {
            $payload['new_stock'] = $this->stock;
        }

        $this->logs()->create($payload);
    }

    /**
     * Restock ingredient and create log
     */
    public function restockIngredient(float $amount, ?string $note = null, ?int $orderId = null, ?int $productId = null, string $type = 'Restock'): void
    {
        $previous = $this->stock;
        $this->stock += $amount;
        $this->updateStatus();
        
        $payload = [
            'change_amount' => $amount,
            'type' => $type,
            'note' => $note,
        ];

        if (Schema::hasColumn('ingredient_logs', 'direction')) {
            $payload['direction'] = 'IN';
        }
        if (Schema::hasColumn('ingredient_logs', 'order_id')) {
            $payload['order_id'] = $orderId;
        }
        if (Schema::hasColumn('ingredient_logs', 'product_id')) {
            $payload['product_id'] = $productId;
        }
        if (Schema::hasColumn('ingredient_logs', 'reference_id')) {
            $payload['reference_id'] = $orderId;
        }
        if (Schema::hasColumn('ingredient_logs', 'previous_stock')) {
            $payload['previous_stock'] = $previous;
        }
        if (Schema::hasColumn('ingredient_logs', 'new_stock')) {
            $payload['new_stock'] = $this->stock;
        }

        $this->logs()->create($payload);
    }

    /**
     * Get formatted stock with unit
     */
    public function getFormattedStockAttribute(): string
    {
        return number_format($this->stock, 2) . ' ' . $this->unit;
    }

    /**
     * Get status badge CSS class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Aman' => 'bg-green-100 text-green-800',
            'Hampir Habis' => 'bg-yellow-100 text-yellow-800',
            'Habis' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status icon for UI
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'Aman' => 'check_circle',
            'Hampir Habis' => 'warning',
            'Habis' => 'cancel',
            default => 'help'
        };
    }

    /**
     * Get status color name
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Aman' => 'green',
            'Hampir Habis' => 'yellow',
            'Habis' => 'red',
            default => 'gray'
        };
    }

    /**
     * Check if ingredient can be deleted
     */
    public function canBeDeleted(): bool
    {
        return $this->recipes()->count() === 0;
    }

    /**
     * Boot method to auto-update status
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ingredient) {
            // Auto-update status before saving
            if ($ingredient->stock <= 0) {
                $ingredient->status = 'Habis';
            } elseif ($ingredient->stock <= $ingredient->minimum_stock) {
                $ingredient->status = 'Hampir Habis';
            } else {
                $ingredient->status = 'Aman';
            }
        });
    }
}
