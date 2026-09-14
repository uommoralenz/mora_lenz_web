<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_super_admin',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(AdminToken::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * The very last active super admin must never be deleted or demoted,
     * otherwise nobody can ever manage admin accounts again.
     */
    public function isLastSuperAdmin(): bool
    {
        if (! $this->isSuperAdmin()) {
            return false;
        }

        return static::query()
            ->where('is_super_admin', true)
            ->where('is_active', true)
            ->where('id', '!=', $this->id)
            ->doesntExist();
    }
}
