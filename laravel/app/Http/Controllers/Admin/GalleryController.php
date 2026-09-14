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
            'data' => FeaturedGallery::orderBy('sort_order')
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
            'image' => ['required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'is_active' => ['boolean'],
        ]);

        $gallery = FeaturedGallery::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'image_url' => ImageStore::put($request->file('image'), 'gallery'),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $this->nextSortOrder(FeaturedGallery::class),
        ]);

        return response()->json(['data' => $this->present($gallery)], 201);
    }

    public function update(Request $request, FeaturedGallery $gallery): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'is_active' => ['boolean'],
        ]);

        if (array_key_exists('title', $data)) {
            $gallery->title = $data['title'];
        }

        if (array_key_exists('description', $data)) {
            $gallery->description = $data['description'];
        }

        if (array_key_exists('is_active', $data)) {
            $gallery->is_active = (bool) $data['is_active'];
        }

        if ($request->hasFile('image')) {
            $gallery->image_url = ImageStore::replace($request->file('image'), 'gallery', $gallery->image_url);
        }

        $gallery->save();

        return response()->json(['data' => $this->present($gallery)]);
    }

    public function destroy(FeaturedGallery $gallery): JsonResponse
    {
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
            'image_url' => $gallery->image_url,
            'sort_order' => (int) $gallery->sort_order,
            'is_active' => (bool) $gallery->is_active,
            'created_at' => $gallery->created_at?->toIso8601String(),
        ];
    }
}
