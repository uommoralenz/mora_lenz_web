<?php

namespace App\Http\Controllers\Public_;

use App\Models\TeamGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('pages.team', [
            'groups' => static::structure(),
        ]);
    }

    /**
     * The whole Group -> Subgroup -> Member tree, active rows only,
     * eager loaded so rendering the page costs a fixed number of queries.
     */
    public static function structure(): Collection
    {
        return TeamGroup::active()
            ->with([
                'directMembers' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'subgroups' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'subgroups.members' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
