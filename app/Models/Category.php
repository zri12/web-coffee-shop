<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
        'option_flags',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'option_flags' => 'array',
    ];

    protected $appends = [
        'option_flags_with_defaults',
    ];

    /**
     * Get menus in this category
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Boot method - auto generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getOptionFlagsWithDefaultsAttribute(): array
    {
        $defaults = config('menu-options.defaults', []);
        $stored = $this->option_flags ?? [];
        if (!is_array($stored)) {
            $stored = [];
        }
        return array_merge($defaults, $stored);
    }
}
