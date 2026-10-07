<?php

namespace App\Http\Controllers\Admin;

use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Generic image upload, for flows where the panel wants a URL before it saves
 * the record it belongs to. Most endpoints accept the file inline instead.
 */
class UploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $types = array_keys(config('moralenz.upload.types'));

        // Gallery photos have a tighter size cap than other images.
        $maxKb = $request->input('type') === 'gallery'
            ? config('moralenz.upload.gallery_max_kb')
            : config('moralenz.upload.max_kb');

        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', $types)],
            'image' => ['required', 'image', 'mimes:'.implode(',', config('moralenz.upload.mimes')), 'max:'.$maxKb],
        ]);

        return response()->json([
            'url' => ImageStore::put($request->file('image'), $data['type']),
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'string', 'max:500'],
        ]);

        // ImageStore ignores anything that does not resolve inside public/uploads.
        ImageStore::delete($data['url']);

        return response()->json(['message' => 'Deleted.']);
    }
}
