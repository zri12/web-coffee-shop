<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'qr_code_path',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Get orders for this table
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'table_number', 'table_number');
    }

    /**
     * Scope for available tables
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for occupied tables
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'green',
            'occupied' => 'red',
            'reserved' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get QR code URL
     */
    public function getQrUrlAttribute(): string
    {
        return url("/table/{$this->table_number}");
    }
}
