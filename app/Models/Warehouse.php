<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'address',
        'location',
        'working_hours',
        'phone',
        'url',
        'is_physical_store',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'location' => 'array',
        'is_physical_store' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Get all products in this warehouse (with stock quantities).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'reserved')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active warehouses.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include physical stores.
     */
    public function scopePhysicalStores($query)
    {
        return $query->where('is_physical_store', true);
    }

    /**
     * Get formatted phone number with tel: link.
     */
    public function getPhoneLinkAttribute(): string
    {
        return $this->phone ? 'tel:' . preg_replace('/[^0-9+]/', '', $this->phone) : '';
    }
}
