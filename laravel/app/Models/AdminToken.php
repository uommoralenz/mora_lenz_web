<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminToken extends Model
{
    protected $hidden = ['token_hash'];

    protected $fillable = [
        'admin_id',
        'token_hash',
        'expires_at',
        'last_used_at',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
