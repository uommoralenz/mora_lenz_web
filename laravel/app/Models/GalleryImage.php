<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = ['gallery_id', 'image_url', 'description', 'sort_order'];

    public function gallery()
    {
        return $this->belongsTo(FeaturedGallery::class, 'gallery_id');
    }
}
