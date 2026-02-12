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

            return asset('images/menus/' . basename($normalized));
        }

        $prompt = rawurlencode(
            'professional cafe menu photography of ' . $this->name . ', soft lighting, high detail'
        );

        return 'https://image.pollinations.ai/prompt/' . $prompt . '?width=1024&height=1024&seed=' . $this->id;
    }

    /**
     * Local placeholder image when remote AI image fails.
     */
    public function getPlaceholderImageUrlAttribute(): string
    {
        return 'https://placehold.co/800x800/F4EDE6/6A4B2A?text=' . rawurlencode($this->name);
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
