<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\Event;
use App\Support\EventBlocks;
use App\Support\ImageStore;
use App\Support\Links;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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

    /**
     * Park the unsaved form in the cache and return a short-lived link to the
     * real public page rendered from it. The token is the only credential, so
     * it is long, random and expires on its own.
     */
    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:20000'],
            'content' => ['nullable'],
            'page_options' => ['nullable'],
            'event_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:200'],
            'image_url' => ['nullable', 'url:http,https', 'max:500'],
            'countdown_enabled' => ['nullable', 'boolean'],
            'event_id' => ['nullable', 'integer'],
        ]);

        $existing = isset($data['event_id']) ? Event::find($data['event_id']) : null;

        $token = Str::random(40);

        Cache::put('event-preview:'.$token, [
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? null,
            'content' => EventBlocks::sanitize($data['content'] ?? []),
            'page_options' => EventBlocks::pageOptions($data['page_options'] ?? []),
            'event_date' => $data['event_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'location' => $data['location'] ?? null,
            'image_url' => ($data['image_url'] ?? null) ?: $existing?->image_url,
            'countdown_enabled' => (bool) ($data['countdown_enabled'] ?? false),
        ], now()->addMinutes(30));

        return response()->json(['url' => Links::eventPreview($token)]);
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
            'content' => ['nullable'],
            'page_options' => ['nullable'],
            'event_date' => [$event ? 'sometimes' : 'required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:event_date'],
            'location' => ['nullable', 'string', 'max:200'],
            'image_url' => ['nullable', 'url:http,https', 'max:500'],
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

        if (array_key_exists('content', $data)) {
            $blocks = EventBlocks::sanitize($data['content']);
            $event->content = $blocks === [] ? null : $blocks;
        }

        if (array_key_exists('page_options', $data)) {
            $options = EventBlocks::pageOptions($data['page_options']);
            // Storing null for an all-defaults page keeps the column meaningful:
            // a row with options set is a page someone deliberately customised.
            $event->page_options = $options === EventBlocks::PAGE_DEFAULTS ? null : $options;
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
            'content' => $event->content ?? [],
            'page_options' => $event->pageOptions(),
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
