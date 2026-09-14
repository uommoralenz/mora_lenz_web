<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\ServiceImage;
use App\Models\ServicePackage;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

/** The rotating hero carousel on each /services/{type} page. */
class ServiceImageController extends Controller
{
    use ReordersRecords;

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('service_type');

        return response()->json([
            'data' => ServiceImage::query()
                ->when($type, fn ($q) => $q->where('service_type', $type))
                ->orderBy('service_type')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($i) => $this->present($i)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_type' => ['required', Rule::in(ServicePackage::TYPES)],
            'caption' => ['nullable', 'string', 'max:200'],
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
        ]);

        $sortOrder = $this->nextSortOrder(ServiceImage::class, [
            'service_type' => $data['service_type'],
        ]);

        $created = [];

        foreach ($request->file('images') as $file) {
            $created[] = $this->present(ServiceImage::create([
                'service_type' => $data['service_type'],
                'image_url' => ImageStore::put($file, 'service-images'),
                'caption' => $data['caption'] ?? null,
                'sort_order' => $sortOrder++,
            ]));
        }

        return response()->json(['data' => $created], 201);
    }

    public function update(Request $request, ServiceImage $serviceImage): JsonResponse
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:200'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
        ]);

        if (array_key_exists('caption', $data)) {
            $serviceImage->caption = $data['caption'];
        }

        if (array_key_exists('is_active', $data)) {
            $serviceImage->is_active = (bool) $data['is_active'];
        }

        if ($request->hasFile('image')) {
            $serviceImage->image_url = ImageStore::replace($request->file('image'), 'service-images', $serviceImage->image_url);
        }

        $serviceImage->save();

        return response()->json(['data' => $this->present($serviceImage)]);
    }

    public function destroy(ServiceImage $serviceImage): JsonResponse
    {
        ImageStore::delete($serviceImage->image_url);
        $serviceImage->delete();

        return response()->json(['message' => 'Image deleted.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        return $this->applyOrder($request, ServiceImage::class);
    }

    protected function present(ServiceImage $image): array
    {
        return [
            'id' => $image->id,
            'service_type' => $image->service_type,
            'image_url' => $image->image_url,
            'caption' => $image->caption,
            'sort_order' => (int) $image->sort_order,
            'is_active' => (bool) $image->is_active,
        ];
    }
}
