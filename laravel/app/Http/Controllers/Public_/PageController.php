<?php

namespace App\Http\Controllers\Public_;

use App\Models\Page;
use App\Models\TeamTerm;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    /** The About page: the admin's own content, then every year's team. */
    public function about(): View
    {
        return view('pages.about', [
            'page' => Page::named('about'),
            'terms' => TeamTerm::active()->newest()->withCount([
                'groups as member_count' => fn ($q) => $q->where('team_groups.is_active', true)
                    ->join('team_members', 'team_members.group_id', '=', 'team_groups.id')
                    ->where('team_members.is_active', true),
            ])->get(),
        ]);
    }
}
