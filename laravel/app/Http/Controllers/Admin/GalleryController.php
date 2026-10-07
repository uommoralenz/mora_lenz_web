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
            'data' => FeaturedGallery::with('images')->withCount('images')->orderBy('sort_order')
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
            'images' => ['nullable', 'array', 'max:'.$this->maxPhotos()],
            'images.*' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.gallery_max_kb')],
            'image_urls' => ['nullable', 'array', 'max:'.$this->maxPhotos()],
            'image_urls.*' => ['string', 'max:500'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ]);

        $urls = $this->photoUrls($request);

        if ($urls === []) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => 'Add at least one photo.',
            ]);
        }

        $gallery = FeaturedGallery::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'facebook_album_url' => $data['facebook_album_url'] ?? null,
            'image_url' => $urls[0],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'show_on_homepage' => (bool) ($data['show_on_homepage'] ?? false),
            'sort_order' => $this->nextSortOrder(FeaturedGallery::class),
        ]);

        foreach ($urls as $index => $url) {
            $gallery->images()->create([
                'image_url' => $url,
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
            'images' => ['nullable', 'array', 'max:'.$this->maxPhotos()],
            'images.*' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.gallery_max_kb')],
            'image_urls' => ['nullable', 'array', 'max:'.$this->maxPhotos()],
            'image_urls.*' => ['string', 'max:500'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ]);

        $newUrls = $this->photoUrls($request);
        $room = $this->maxPhotos() - $gallery->images()->count();

        if (count($newUrls) > $room) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => 'An album can hold up to '.$this->maxPhotos().' photos. '
                    .($room > 0 ? "You can add {$room} more." : 'Remove a photo before adding another.'),
            ]);
        }

        if (array_key_exists('title', $data)) {
            $gallery->title = $data['title'];
        }

        if (array_key_exists('description', $data)) {
            $gallery->description = $data['description'];
        }

        if (array_key_exists('facebook_album_url', $data)) {
            $gallery->facebook_album_url = $data['facebook_album_url'];
        }

        if (array_key_exists('is_active', $data)) {
            $gallery->is_active = (bool) $data['is_active'];
        }

        foreach ($newUrls as $stored) {
            $gallery->images()->create([
                'image_url' => $stored,
                'description' => null,
                'sort_order' => $gallery->images()->max('sort_order') + 1,
            ]);
            if ($gallery->image_url === null) {
                $gallery->image_url = $stored;
            }
        }

        if (array_key_exists('show_on_homepage', $data)) {
            $gallery->show_on_homepage = (bool) $data['show_on_homepage'];
        }

        $gallery->save();

        return response()->json(['data' => $this->present($gallery->loadCount('images'))]);
    }

    /**
     * Every photo in the request as a stored URL, in order: photos the panel
     * already uploaded one at a time (image_urls — only our own files are
     * accepted) followed by any files sent directly.
     */
    protected function photoUrls(Request $request): array
    {
        $urls = [];

        foreach ((array) $request->input('image_urls', []) as $url) {
            if (is_string($url) && ImageStore::ownsUrl($url, 'gallery')) {
                $urls[] = $url;
            }
        }

        foreach (array_filter((array) $request->file('images', [])) as $file) {
            $urls[] = ImageStore::put($file, 'gallery');
        }

        return array_values(array_unique($urls));
    }

    /** Remove a single photo from an album. An album always keeps at least one. */
    public function destroyImage(FeaturedGallery $gallery, GalleryImage $image): JsonResponse
    {
        abort_unless($image->gallery_id === $gallery->id, 404);

        if ($gallery->images()->count() <= 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'images' => 'An album needs at least one photo. Delete the whole album instead.',
            ]);
        }

        ImageStore::delete($image->image_url);
        $image->delete();

        // The cover follows the first remaining photo.
        $first = $gallery->images()->first();
        $gallery->image_url = $first?->image_url;
        $gallery->save();

        return response()->json(['data' => $this->present($gallery->loadCount('images')->load('images'))]);
    }

    protected function maxPhotos(): int
    {
        return (int) config('moralenz.upload.gallery_max_photos', 5);
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

    protected function present(FeaturedGallery $gallery): array
    {
        return [
            'id' => $gallery->id,
            'title' => $gallery->title,
            'description' => $gallery->description,
            'facebook_album_url' => $gallery->facebook_album_url,
            'image_url' => $gallery->image_url,
            'image_count' => (int) ($gallery->images_count ?? $gallery->images()->count()),
            'photos' => $gallery->relationLoaded('images')
                ? $gallery->images->map(fn ($i) => ['id' => $i->id, 'url' => $i->image_url])->values()
                : [],
            'sort_order' => (int) $gallery->sort_order,
            'is_active' => (bool) $gallery->is_active,
            'show_on_homepage' => (bool) $gallery->show_on_homepage,
            'created_at' => $gallery->created_at?->toIso8601String(),
        ];
    }
}
