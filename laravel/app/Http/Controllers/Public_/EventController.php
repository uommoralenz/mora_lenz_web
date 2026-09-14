<?php

namespace App\Http\Controllers\Public_;

use App\Models\Event;
use Illuminate\Routing\Controller;
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
}
