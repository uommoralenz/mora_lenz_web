<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    protected $fillable = [
        'group_id',
        'subgroup_id',
        'name',
        'profession',
        'description',
        'image_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(TeamGroup::class, 'group_id');
    }

    public function subgroup(): BelongsTo
    {
        return $this->belongsTo(TeamSubgroup::class, 'subgroup_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** First letter, shown in the avatar circle when there is no photo. */
    public function initial(): string
    {
        return Str::upper(Str::substr(trim($this->name), 0, 1)) ?: '?';
    }
}
