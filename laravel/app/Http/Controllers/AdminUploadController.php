<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $configuredToken = (string) config('services.admin_upload.token');
        $providedToken = (string) $request->bearerToken();

        if ($configuredToken === '') {
            return response()->json(['message' => 'Upload token is not configured.'], 503);
        }

        if (! hash_equals($configuredToken, $providedToken)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:events,members,gallery'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:6144'],
        ]);

        $image = $validated['image'];
        $directory = public_path('uploads/'.$validated['type']);

        File::ensureDirectoryExists($directory);

        $filename = Str::uuid().'.'.$image->extension();
        $image->move($directory, $filename);

        $path = '/uploads/'.$validated['type'].'/'.$filename;

        return response()->json([
            'path' => $path,
            'url' => asset(ltrim($path, '/')),
        ], 201);
    }
}
