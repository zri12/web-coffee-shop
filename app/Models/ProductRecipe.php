<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
    ];

    /**
     * Get the product (menu item)
     */
    public function product()
    {
        return $this->belongsTo(Menu::class, 'product_id');
    }

    /**
     * Get the ingredient
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Get total cost for this recipe item (if ingredient has cost)
     */
    public function getTotalCostAttribute(): float
    {
        // Future enhancement: add cost_per_unit to ingredients table
        return 0;
    }

    /**
     * Get formatted quantity with unit
     */
    public function getFormattedQuantityAttribute(): string
    {
        return number_format($this->quantity_used, 2) . ' ' . $this->ingredient->unit;
    }
}
