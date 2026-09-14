<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\Event;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    use ReordersRecords;

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Event::orderBy('sort_order')
                ->orderBy('event_date')
                ->get()
                ->map(fn (Event $e) => $this->present($e)),
        ]);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json(['data' => $this->present($event)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $event = new Event;
        $this->fill($event, $data, $request);
        $event->sort_order = $this->nextSortOrder(Event::class);
        $event->save();

        $this->syncFeatured($event);

        return response()->json(['data' => $this->present($event->fresh())], 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $this->validated($request, $event);

        $this->fill($event, $data, $request);
        $event->save();

        $this->syncFeatured($event);

        return response()->json(['data' => $this->present($event->fresh())]);
    }

    public function destroy(Event $event): JsonResponse
    {
        ImageStore::delete($event->image_url);
        $event->delete();

        return response()->json(['message' => 'Event deleted.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        return $this->applyOrder($request, Event::class);
    }

    protected function validated(Request $request, ?Event $event = null): array
    {
        return $request->validate([
            'title' => [$event ? 'sometimes' : 'required', 'string', 'max:200'],
            'slug' => [
                'nullable', 'string', 'max:200', 'alpha_dash',
                Rule::unique('events', 'slug')->ignore($event?->id),
            ],
            'description' => ['nullable', 'string', 'max:20000'],
            'event_date' => [$event ? 'sometimes' : 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'location' => ['nullable', 'string', 'max:200'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'countdown_enabled' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ]);
    }

    protected function fill(Event $event, array $data, Request $request): void
    {
        foreach (['title', 'description', 'event_date', 'end_date', 'location'] as $field) {
            if (array_key_exists($field, $data)) {
                $event->{$field} = $data[$field];
            }
        }

        foreach (['countdown_enabled', 'is_featured', 'is_active'] as $flag) {
            if (array_key_exists($flag, $data)) {
                $event->{$flag} = (bool) $data[$flag];
            }
        }

        // A blank slug means "regenerate from the title".
        if (array_key_exists('slug', $data)) {
            $event->slug = filled($data['slug'])
                ? Event::uniqueSlug($data['slug'], $event->id)
                : Event::uniqueSlug($event->title ?: 'event', $event->id);
        }

        if ($request->hasFile('image')) {
            $event->image_url = ImageStore::replace($request->file('image'), 'events', $event->image_url);
        } elseif (array_key_exists('image_url', $data)) {
            // Clearing the field removes the stored file too.
            if (blank($data['image_url']) && filled($event->image_url)) {
                ImageStore::delete($event->image_url);
            }
            $event->image_url = $data['image_url'];
        }
    }

    /** Only one event is the hero event, so flagging one unflags the rest. */
    protected function syncFeatured(Event $event): void
    {
        if ($event->is_featured) {
            Event::where('id', '!=', $event->id)
                ->where('is_featured', true)
                ->update(['is_featured' => false]);
        }
    }

    protected function present(Event $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description,
            'event_date' => $event->event_date?->toIso8601String(),
            'end_date' => $event->end_date?->toIso8601String(),
            'location' => $event->location,
            'image_url' => $event->image_url,
            'countdown_enabled' => (bool) $event->countdown_enabled,
            'is_featured' => (bool) $event->is_featured,
            'is_active' => (bool) $event->is_active,
            'sort_order' => (int) $event->sort_order,
            'created_at' => $event->created_at?->toIso8601String(),
        ];
    }
}
