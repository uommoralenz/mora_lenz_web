<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function subgroups(): HasMany
    {
        return $this->hasMany(TeamSubgroup::class, 'group_id')->orderBy('sort_order');
    }

    /** Every member in the group, whether or not they sit in a subgroup. */
    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'group_id')->orderBy('sort_order');
    }

    /** Members attached straight to the group, with no subgroup. */
    public function directMembers(): HasMany
    {
        return $this->members()->whereNull('subgroup_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
