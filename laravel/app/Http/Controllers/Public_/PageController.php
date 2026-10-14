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
            'terms' => TeamTerm::listing(),
        ]);
    }
}
