<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\FeaturedGallery;
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
            'data' => FeaturedGallery::with('images')->orderBy('sort_order')
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
            'facebook_album_url' => ['required', 'url', 'max:2048'],
            'images' => ['required', 'array', 'min:1', 'max:30'],
            'images.*' => ['required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'image_descriptions' => ['nullable', 'array'],
            'image_descriptions.*' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ]);

        $gallery = FeaturedGallery::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'facebook_album_url' => $data['facebook_album_url'] ?? null,
            'image_url' => ImageStore::put($request->file('images')[0], 'gallery'),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'show_on_homepage' => (bool) ($data['show_on_homepage'] ?? false),
            'sort_order' => $this->nextSortOrder(FeaturedGallery::class),
        ]);

        foreach ($request->file('images') as $index => $image) {
            $gallery->images()->create([
                'image_url' => $index === 0 ? $gallery->image_url : ImageStore::put($image, 'gallery'),
                'description' => $data['image_descriptions'][$index] ?? null,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['data' => $this->present($gallery->load('images'))], 201);
    }

    public function update(Request $request, FeaturedGallery $gallery): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'facebook_album_url' => ['nullable', 'url', 'max:2048'],
            'images' => ['nullable', 'array', 'max:30'],
            'images.*' => ['required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'image_descriptions' => ['nullable', 'array'],
            'image_descriptions.*' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['boolean'],
            'show_on_homepage' => ['boolean'],
        ]);

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

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $stored = ImageStore::put($image, 'gallery');
                $gallery->images()->create([
                    'image_url' => $stored,
                    'description' => $data['image_descriptions'][$index] ?? null,
                    'sort_order' => $gallery->images()->max('sort_order') + 1,
                ]);
                if ($gallery->image_url === null) $gallery->image_url = $stored;
            }
        }

        if (array_key_exists('show_on_homepage', $data)) {
            $gallery->show_on_homepage = (bool) $data['show_on_homepage'];
        }

        $gallery->save();

        return response()->json(['data' => $this->present($gallery->load('images'))]);
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
            'images' => $gallery->images->map(fn ($image) => [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'description' => $image->description,
                'sort_order' => (int) $image->sort_order,
            ])->values(),
            'sort_order' => (int) $gallery->sort_order,
            'is_active' => (bool) $gallery->is_active,
            'show_on_homepage' => (bool) $gallery->show_on_homepage,
            'created_at' => $gallery->created_at?->toIso8601String(),
        ];
    }
}
