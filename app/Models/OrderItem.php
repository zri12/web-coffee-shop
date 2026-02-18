<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'menu_name',
        'quantity',
        'price',
        'unit_price',
        'subtotal',
        'notes',
        'options',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'options' => 'array',
    ];

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the menu item
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Boot - auto calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::updating(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        // After save, recalculate order total
        static::saved(function ($item) {
            $item->order->calculateTotal();
        });

        static::deleted(function ($item) {
            $item->order->calculateTotal();
        });
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get options as readable text
     */
    public function getOptionsTextAttribute(): string
    {
        if (!$this->options || empty($this->options)) {
            // Fallback: show notes if options not stored
            return $this->notes ?? '';
        }

        $texts = [];
        $options = $this->options;

        // Temperature & ice
        if (isset($options['temperature'])) {
            $temp = $options['temperature'] === 'ice' ? 'Ice' : 'Hot';
            $texts[] = "🌡️ {$temp}";
            
            if ($options['temperature'] === 'ice' && isset($options['iceLevel'])) {
                $iceLevel = ucfirst($options['iceLevel']);
                if ($options['iceLevel'] !== 'normal') {
                    $texts[] = "🧊 {$iceLevel} Ice";
                }
            }
        }

        // Sugar level
        if (isset($options['sugarLevel']) && $options['sugarLevel'] !== 'normal') {
            $sugar = ucfirst($options['sugarLevel']);
            $texts[] = "🍯 {$sugar} Sugar";
        }

        // Size
        if (isset($options['size']) && $options['size'] !== 'regular') {
            $size = ucfirst($options['size']);
            $texts[] = "📏 {$size}";
        }

        // Portion  
        if (isset($options['portion']) && $options['portion'] !== 'regular') {
            $portion = ucfirst($options['portion']);
            $texts[] = "🍽️ {$portion}";
        }

        // Spice level
        if (isset($options['spiceLevel']) && $options['spiceLevel'] !== 'mild') {
            $spice = ucfirst($options['spiceLevel']);
            $texts[] = "🌶️ {$spice}";
        }

        // Add-ons
        if (isset($options['addOns']) && is_array($options['addOns'])) {
            foreach ($options['addOns'] as $addon) {
                $texts[] = "➕ " . str_replace('-', ' ', ucwords($addon, '-'));
            }
        }

        // Sauces
        if (isset($options['sauces']) && is_array($options['sauces'])) {
            foreach ($options['sauces'] as $sauce) {
                $texts[] = "🥫 " . str_replace('-', ' ', ucwords($sauce, '-'));
            }
        }

        // Toppings
        if (isset($options['toppings']) && is_array($options['toppings'])) {
            foreach ($options['toppings'] as $topping) {
                $texts[] = "🍰 " . str_replace('-', ' ', ucwords($topping, '-'));
            }
        }

        // Special request
        if (isset($options['specialRequest']) && !empty($options['specialRequest'])) {
            $texts[] = "📝 " . $options['specialRequest'];
        }

        return implode(', ', $texts);
    }

    /**
     * Check if item has customizations
     */
    public function hasCustomizations(): bool
    {
        return !empty($this->options) && count($this->options) > 0;
    }
}
