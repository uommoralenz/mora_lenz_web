<?php

namespace App\Http\Controllers\Public_;

use App\Models\Event;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends Controller
{
    public function index(): View
    {
        return view('pages.events', [
            'events' => Event::active()
                ->orderBy('sort_order')
                ->orderBy('event_date')
                ->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $event = Event::active()->where('slug', $slug)->first();

        if (! $event) {
            throw new NotFoundHttpException('Event not found.');
        }

        return view('pages.event-show', [
            'event' => $event,
        ]);
    }

    /** Admin-only draft, rendered from the cache entry the panel created. */
    public function preview(string $token): View
    {
        $draft = Cache::get('event-preview:'.$token);

        if (! is_array($draft)) {
            throw new NotFoundHttpException('This preview has expired. Open it again from the admin panel.');
        }

        $event = new Event([
            'title' => $draft['title'] !== '' ? $draft['title'] : 'Untitled event',
            'description' => $draft['description'],
            'content' => $draft['content'],
            // A draft cached before the layout options existed has none;
            // pageOptions() fills in the defaults for it.
            'page_options' => $draft['page_options'] ?? null,
            'location' => $draft['location'],
            'image_url' => $draft['image_url'],
            'countdown_enabled' => $draft['countdown_enabled'],
        ]);
        $event->event_date = $draft['event_date'] ? Carbon::parse($draft['event_date']) : now()->addWeek();
        $event->end_date = $draft['end_date'] ? Carbon::parse($draft['end_date']) : null;

        return view('pages.event-show', [
            'event' => $event,
            'preview' => true,
        ]);
    }
}
