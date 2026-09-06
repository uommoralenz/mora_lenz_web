<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelPillar extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'panels_pillars';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'icon',
        'name',
        'description',
        'member_count',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'member_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }
}
