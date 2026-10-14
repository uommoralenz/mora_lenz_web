<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A free-form page (only "about" for now), built from the same blocks and
 * layout options as an event page.
 */
class Page extends Model
{
    /** The pages that exist. Each one is created by its migration. */
    public const SLUGS = ['about'];

    protected $fillable = [
        'slug',
        'title',
        'description',
        'content',
        'page_options',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'page_options' => 'array',
        ];
    }

    public function pageOptions(): array
    {
        return \App\Support\EventBlocks::pageOptions($this->page_options);
    }

    /** The row for a slug, created on the spot if a migration was skipped. */
    public static function named(string $slug): self
    {
        return static::firstOrCreate(['slug' => $slug], ['title' => ucfirst($slug)]);
    }
}
