<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'image_urls',
        'status',
        'sort_order',
    ];

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'image_urls' => 'array',
        ];
    }

    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->where('status', 'upcoming');
    }

    #[Scope]
    protected function past(Builder $query): void
    {
        $query->where('status', 'past');
    }

    public function coverImage(): string
    {
        return $this->image_urls[0] ?? '/events/past/media-awards-2025.webp';
    }
}
