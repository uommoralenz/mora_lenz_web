<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\FeaturedGallery;
use App\Models\GalleryImage;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GalleryController extends Controller
{
    use ReordersRecords;

    public function index(): JsonResponse
    {
        return response()->json([
            // The admin list only needs the cover and a photo count. Returning
            // every URL in every album can exceed Vercel's function payload
            // limit once the gallery grows.
            'data' => FeaturedGallery::withCount('images')->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($g) => $this->present($g)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'facebook_album_url' => ['nullable', 'url', 'max:2048'],
            'facebook_album_urls' => ['nullable', 'array', 'max:10'],
            'facebook_album_urls.*' => ['nullable', 'url', 'max:2048'],
            'images' => ['required', 'array', 'min:1', 'max:20'],
            'images.*' => ['required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ]);

        $gallery = FeaturedGallery::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            ...$this->facebookFields($data),
            'image_url' => ImageStore::put($request->file('images')[0], 'gallery'),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'show_on_homepage' => (bool) ($data['show_on_homepage'] ?? false),
            'sort_order' => $this->nextSortOrder(FeaturedGallery::class),
        ]);

        foreach ($request->file('images') as $index => $image) {
            $gallery->images()->create([
                'image_url' => $index === 0 ? $gallery->image_url : ImageStore::put($image, 'gallery'),
                'description' => null,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['data' => $this->present($gallery->loadCount('images'))], 201);
    }

    public function update(Request $request, FeaturedGallery $gallery): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'facebook_album_url' => ['nullable', 'url', 'max:2048'],
            'facebook_album_urls' => ['nullable', 'array', 'max:10'],
            'facebook_album_urls.*' => ['nullable', 'url', 'max:2048'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ]);

        if (array_key_exists('title', $data)) {
            $gallery->title = $data['title'];
        }

        if (array_key_exists('description', $data)) {
            $gallery->description = $data['description'];
        }

        if (array_key_exists('facebook_album_urls', $data) || array_key_exists('facebook_album_url', $data)) {
            $gallery->fill($this->facebookFields($data));
        }

        if (array_key_exists('is_active', $data)) {
            $gallery->is_active = (bool) $data['is_active'];
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $stored = ImageStore::put($image, 'gallery');
                $gallery->images()->create([
                    'image_url' => $stored,
                    'description' => null,
                    'sort_order' => $gallery->images()->max('sort_order') + 1,
                ]);
                if ($gallery->image_url === null) $gallery->image_url = $stored;
            }
        }

        if (array_key_exists('show_on_homepage', $data)) {
            $gallery->show_on_homepage = (bool) $data['show_on_homepage'];
        }

        $gallery->save();

        return response()->json(['data' => $this->present($gallery->loadCount('images'))]);
    }

    /** The photos of one album, for the edit form. */
    public function photos(FeaturedGallery $gallery): JsonResponse
    {
        return response()->json([
            'data' => $gallery->images->map(fn ($i) => ['id' => $i->id, 'image_url' => $i->image_url])->values(),
        ]);
    }

    public function destroyPhoto(FeaturedGallery $gallery, GalleryImage $image): JsonResponse
    {
        abort_unless($image->gallery_id === $gallery->id, 404);

        $url = $image->image_url;
        $image->delete();

        // The cover is a copy of one photo's address; move it on if that photo went.
        if ($gallery->image_url === $url) {
            $gallery->image_url = $gallery->images()->value('image_url');
            $gallery->save();
        }

        ImageStore::delete($url);

        return response()->json(['message' => 'Photo removed.']);
    }

    public function destroy(FeaturedGallery $gallery): JsonResponse
    {
        foreach ($gallery->images as $image) ImageStore::delete($image->image_url);
        ImageStore::delete($gallery->image_url);
        $gallery->delete();

        return response()->json(['message' => 'Gallery entry deleted.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        return $this->applyOrder($request, FeaturedGallery::class);
    }

    /**
     * Normalise the album links. The list is the source of truth; the legacy
     * single column mirrors its first entry so older code keeps working.
     */
    protected function facebookFields(array $data): array
    {
        $links = $data['facebook_album_urls'] ?? (filled($data['facebook_album_url'] ?? null) ? [$data['facebook_album_url']] : []);
        $links = array_values(array_unique(array_filter(array_map('trim', (array) $links))));

        return [
            'facebook_album_urls' => $links ?: null,
            'facebook_album_url' => $links[0] ?? null,
        ];
    }

    protected function present(FeaturedGallery $gallery): array
    {
        return [
            'id' => $gallery->id,
            'title' => $gallery->title,
            'description' => $gallery->description,
            'facebook_album_url' => $gallery->facebook_album_url,
            'facebook_album_urls' => $gallery->facebookLinks(),
            'image_url' => $gallery->image_url,
            'image_count' => (int) ($gallery->images_count ?? $gallery->images()->count()),
            'sort_order' => (int) $gallery->sort_order,
            'is_active' => (bool) $gallery->is_active,
            'show_on_homepage' => (bool) $gallery->show_on_homepage,
            'created_at' => $gallery->created_at?->toIso8601String(),
        ];
    }
}
