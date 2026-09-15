<?php

namespace App\Http\Controllers\Public_;

use App\Models\Event;
use App\Models\FeaturedGallery;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home', [
            'featuredEvent' => static::featuredEvent(),
            'galleries' => FeaturedGallery::with('images')->active()
                ->where('show_on_homepage', true)
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->limit(3)
                ->get(),
        ]);
    }

    /**
     * The event shown in the homepage hero.
     *
     * Prefers whatever the admin flagged as featured; if nothing is flagged,
     * falls back to the soonest upcoming event so the section is never empty
     * by accident.
     */
    public static function featuredEvent(): ?Event
    {
        $featured = Event::active()
            ->where('is_featured', true)
            ->orderBy('event_date')
            ->first();

        if ($featured) {
            return $featured;
        }

        return Event::active()
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->first();
    }
}
