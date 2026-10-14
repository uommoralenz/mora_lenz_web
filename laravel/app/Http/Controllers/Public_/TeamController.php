<?php

namespace App\Http\Controllers\Public_;

use App\Models\TeamGroup;
use App\Models\TeamTerm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends Controller
{
    /** The team serving now, with links to every earlier year. */
    public function index(): View
    {
        return $this->render(TeamTerm::current());
    }

    /** One year's team, at /team/{slug}. */
    public function show(string $slug): View
    {
        $term = TeamTerm::active()->where('slug', $slug)->first();

        if (! $term) {
            throw new NotFoundHttpException('Team not found.');
        }

        return $this->render($term);
    }

    protected function render(?TeamTerm $term): View
    {
        return view('pages.team', [
            'term' => $term,
            'terms' => TeamTerm::active()->newest()->get(),
            'groups' => $term ? static::structure($term) : new Collection,
        ]);
    }

    /**
     * One term's Group -> Subgroup -> Member tree, active rows only,
     * eager loaded so rendering the page costs a fixed number of queries.
     */
    public static function structure(TeamTerm $term): Collection
    {
        // Members come back ranked first, then in their own order within that
        // rank, so the view can group each collection straight into tier rows.
        $ranked = fn ($q) => $q->where('is_active', true)->orderBy('tier')->orderBy('sort_order');

        return TeamGroup::active()
            ->where('term_id', $term->id)
            ->with([
                'directMembers' => $ranked,
                'subgroups' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'subgroups.members' => $ranked,
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
