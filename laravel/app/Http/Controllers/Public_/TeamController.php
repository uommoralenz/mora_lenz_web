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
        // Members come back ranked first, then in their own order within that
        // rank, so the view can group each collection straight into tier rows.
        $ranked = fn ($q) => $q->where('is_active', true)->orderBy('tier')->orderBy('sort_order');

        return TeamGroup::active()
            ->with([
                'directMembers' => $ranked,
                'subgroups' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'subgroups.members' => $ranked,
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
