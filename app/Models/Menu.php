<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_url',
        'is_available',
        'is_featured',
        'addons',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'addons' => 'array',
    ];

    /**
     * Get image attribute (alias for image_url for backward compatibility)
     */
    public function getImageAttribute()
    {
        return $this->image_url;
    }

    /**
     * Resolve menu image URL with resilient fallbacks for serverless deployments.
     */
    public function getDisplayImageUrlAttribute(): string
    {
        foreach ([$this->image_url, $this->image] as $candidate) {
            if (!$candidate || !is_string($candidate)) {
                continue;
            }

            if (Str::startsWith($candidate, ['http://', 'https://'])) {
                return $candidate;
            }

            $normalized = ltrim($candidate, '/');

            if (str_contains($normalized, 'menu-images/')) {
                return asset('storage/' . $normalized);
            }
        }

        return $this->ai_image_url;
    }

    /**
     * AI photo URL based on menu name/category.
     */
    public function getAiImageUrlAttribute(): string
    {
        $typeHint = 'food photography';
        $name = strtolower($this->name ?? '');

        if (str_contains($name, 'latte') || str_contains($name, 'coffee') || str_contains($name, 'brew') || str_contains($name, 'espresso') || str_contains($name, 'cappuccino')) {
            $typeHint = 'coffee drink product photography';
        } elseif (str_contains($name, 'cake') || str_contains($name, 'dessert') || str_contains($name, 'tiramisu')) {
            $typeHint = 'dessert product photography';
        } elseif (str_contains($name, 'croissant') || str_contains($name, 'bread') || str_contains($name, 'snack')) {
            $typeHint = 'bakery food photography';
        }

        return route('menu.ai-image', [
            'menu' => 'ai-' . $this->id,
            'name' => $this->name,
            'hint' => $typeHint,
        ]);
    }

    /**
     * Local placeholder image when remote AI image fails.
     */
    public function getPlaceholderImageUrlAttribute(): string
    {
        return route('menu.ai-image', [
            'menu' => 'placeholder-' . $this->id,
            'name' => $this->name ?: 'Menu',
        ]);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get order items containing this menu
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get product recipes (ingredients used in this menu item)
     */
    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class, 'product_id');
    }

    /**
     * Scope for available menus
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for featured menus
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope with active category
     */
    public function scopeWithActiveCategory($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Check if menu has insufficient stock for all ingredients
     * 
     * @return bool True if any ingredient is insufficient
     */
    public function hasInsufficientStock(): bool
    {
        $recipes = $this->recipes()->with('ingredient')->get();
        
        // No recipe = always available
        if ($recipes->isEmpty()) {
            return false;
        }
        
        foreach ($recipes as $recipe) {
            if (!$recipe->ingredient) {
                continue;
            }
            
            // Check if stock is less than required quantity
            if ($recipe->ingredient->stock < $recipe->quantity_used) {
                \Log::info("Menu '{$this->name}' has insufficient stock for ingredient '{$recipe->ingredient->name}' (Stock: {$recipe->ingredient->stock}, Required: {$recipe->quantity_used})");
                return true;
            }
        }
        
        return false;
    }

    /**
     * Update menu availability based on current stock levels
     * 
     * @return void
     */
    public function updateAvailabilityByStock(): void
    {
        $hasInsufficientStock = $this->hasInsufficientStock();
        
        if ($hasInsufficientStock && $this->is_available) {
            // Auto-disable menu due to insufficient stock
            $this->update(['is_available' => false]);
            \Log::info("Menu '{$this->name}' auto-disabled due to insufficient stock");
        } elseif (!$hasInsufficientStock && !$this->is_available) {
            // Auto-enable menu when stock is sufficient
            $this->update(['is_available' => true]);
            \Log::info("Menu '{$this->name}' auto-enabled due to sufficient stock");
        }
    }

    /**
     * Get stock status message for this menu
     * 
     * @return string|null
     */
    public function getStockStatusMessage(): ?string
    {
        $recipes = $this->recipes()->with('ingredient')->get();
        
        if ($recipes->isEmpty()) {
            return null;
        }
        
        $insufficientIngredients = [];
        
        foreach ($recipes as $recipe) {
            if (!$recipe->ingredient) continue;
            
            if ($recipe->ingredient->stock < $recipe->quantity_used) {
                $insufficientIngredients[] = $recipe->ingredient->name;
            }
        }
        
        if (empty($insufficientIngredients)) {
            return null;
        }
        
        return 'Stok habis: ' . implode(', ', $insufficientIngredients);
    }

    /**
     * Normalize addons payload for storage.
     */
    public static function normalizeAddons(?array $addons): array
    {
        if (empty($addons) || !is_array($addons)) {
            return [];
        }

        $normalized = [];

        foreach ($addons as $addon) {
            $name = trim($addon['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $priceValue = $addon['price'] ?? 0;
            $price = is_numeric($priceValue) ? (float) $priceValue : 0;

            $normalized[] = [
                'name' => $name,
                'price' => max(0, $price),
            ];
        }

        return array_values($normalized);
    }
}
