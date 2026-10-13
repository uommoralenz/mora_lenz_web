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
        'facebook_album_url',
        'facebook_album_urls',
        'image_url',
        'sort_order',
        'is_active',
        'show_on_homepage',
    ];

    protected function casts(): array
    {
        return [
            'facebook_album_urls' => 'array',
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Every Facebook album linked to this gallery, falling back to the legacy single link. */
    public function facebookLinks(): array
    {
        $links = array_values(array_filter((array) $this->facebook_album_urls));

        if ($links === [] && $this->facebook_album_url) {
            $links = [$this->facebook_album_url];
        }

        return $links;
    }

    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'gallery_id')->orderBy('sort_order')->orderBy('id');
    }
}
