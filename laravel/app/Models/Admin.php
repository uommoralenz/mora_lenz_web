<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Model
{
    /** Sections a non-super admin can be given. Keep in sync with the panel. */
    public const PERMISSIONS = ['events', 'gallery', 'services', 'team', 'messages'];

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_super_admin',
        'permissions',
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
            'permissions' => 'array',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(AdminToken::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // 'any' means "at least one section" (used for shared endpoints like uploads).
        if ($permission === 'any') {
            return $this->effectivePermissions() !== [];
        }

        return in_array($permission, $this->effectivePermissions(), true);
    }

    /** What the panel should show: everything for a super admin. */
    public function effectivePermissions(): array
    {
        return $this->isSuperAdmin()
            ? self::PERMISSIONS
            : array_values(array_intersect(self::PERMISSIONS, $this->permissions ?? []));
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
