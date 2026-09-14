<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    public const TYPES = ['photography', 'videography'];

    protected $fillable = [
        'service_type',
        'name',
        'description',
        'price',
        'offered_price',
        'image_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            // A JSON array of bullet-point strings.
            'description' => 'array',
            'price' => 'decimal:2',
            'offered_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('service_type', $type);
    }

    /** Bullet points, always an array even if the column holds null. */
    public function points(): array
    {
        return array_values(array_filter(
            (array) ($this->description ?? []),
            fn ($line) => trim((string) $line) !== ''
        ));
    }
}
