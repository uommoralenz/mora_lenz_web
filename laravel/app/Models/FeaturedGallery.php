<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeaturedGallery extends Model
{
    protected $table = 'featured_galleries';

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'sort_order',
        'is_active',
        'show_on_homepage',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
