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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
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
}
