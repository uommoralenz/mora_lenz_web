<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * One year's team. The committee changes every year, so each term keeps its
 * own groups, subgroups and members, and has its own page at /team/{slug}.
 */
class TeamTerm extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'year',
        'description',
        'is_current',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_current' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function groups(): HasMany
    {
        return $this->hasMany(TeamGroup::class, 'term_id')->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Newest first, which is how every list of terms is shown. */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderByDesc('year')->orderByDesc('id');
    }

    /** The term /team shows: the one marked current, else the newest. */
    public static function current(): ?self
    {
        return static::active()->where('is_current', true)->first()
            ?? static::active()->newest()->first();
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug(str_replace('/', '-', $name)) ?: 'team';
        $slug = $base;
        $suffix = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
