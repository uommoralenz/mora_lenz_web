<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ReordersRecords;
use App\Models\ServicePackage;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class ServicePackageController extends Controller
{
    use ReordersRecords;

    public function index(Request $request): JsonResponse
    {
        $type = $request->query('service_type');

        return response()->json([
            'data' => ServicePackage::query()
                ->when($type, fn ($q) => $q->where('service_type', $type))
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($p) => $this->present($p)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);

        $package = new ServicePackage;
        $this->fill($package, $data, $request);
        $package->sort_order = $this->nextSortOrder(ServicePackage::class, [
            'service_type' => $package->service_type,
        ]);
        $package->save();

        return response()->json(['data' => $this->present($package)], 201);
    }

    public function update(Request $request, ServicePackage $package): JsonResponse
    {
        $data = $this->validated($request, $package);

        $this->fill($package, $data, $request);
        $package->save();

        return response()->json(['data' => $this->present($package)]);
    }

    public function destroy(ServicePackage $package): JsonResponse
    {
        ImageStore::delete($package->image_url);
        $package->delete();

        return response()->json(['message' => 'Package deleted.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        return $this->applyOrder($request, ServicePackage::class);
    }

    protected function validated(Request $request, ?ServicePackage $package = null): array
    {
        return $request->validate([
            'service_type' => [$package ? 'sometimes' : 'required', Rule::in(ServicePackage::TYPES)],
            'name' => [$package ? 'sometimes' : 'required', 'string', 'max:200'],
            // Accepted either as a newline separated string or as a JSON array.
            'description' => ['nullable'],
            'price' => [$package ? 'sometimes' : 'required', 'numeric', 'min:0', 'max:99999999'],
            'offered_price' => ['nullable', 'numeric', 'min:0', 'max:99999999', 'lt:price'],
            'image' => [$package ? 'nullable' : 'required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.config('moralenz.upload.max_kb')],
            'is_active' => ['boolean'],
        ], [
            'offered_price.lt' => 'The offer price must be lower than the normal price.',
        ]);
    }

    protected function fill(ServicePackage $package, array $data, Request $request): void
    {
        foreach (['service_type', 'name', 'price'] as $field) {
            if (array_key_exists($field, $data)) {
                $package->{$field} = $data[$field];
            }
        }

        if (array_key_exists('offered_price', $data)) {
            $package->offered_price = $data['offered_price'] === '' ? null : $data['offered_price'];
        }

        if (array_key_exists('description', $data)) {
            $package->description = $this->toPoints($data['description']);
        }

        if (array_key_exists('is_active', $data)) {
            $package->is_active = (bool) $data['is_active'];
        }

        if ($request->hasFile('image')) {
            $package->image_url = ImageStore::replace($request->file('image'), 'services', $package->image_url);
        }
    }

    /** Normalise bullet points into a clean array of non-empty lines. */
    protected function toPoints(mixed $value): array
    {
        if (is_array($value)) {
            $lines = $value;
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            $lines = is_array($decoded) ? $decoded : preg_split('/\r\n|\r|\n/', $value);
        } else {
            $lines = [];
        }

        return array_values(array_filter(
            array_map(fn ($line) => trim((string) $line), $lines),
            fn ($line) => $line !== ''
        ));
    }

    protected function present(ServicePackage $package): array
    {
        return [
            'id' => $package->id,
            'service_type' => $package->service_type,
            'name' => $package->name,
            'description' => $package->points(),
            'price' => (float) $package->price,
            'offered_price' => $package->offered_price !== null ? (float) $package->offered_price : null,
            'image_url' => $package->image_url,
            'sort_order' => (int) $package->sort_order,
            'is_active' => (bool) $package->is_active,
            'created_at' => $package->created_at?->toIso8601String(),
        ];
    }
}
