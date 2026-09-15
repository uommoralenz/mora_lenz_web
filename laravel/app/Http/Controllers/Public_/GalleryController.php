<?php

namespace App\Http\Controllers\Public_;

use App\Models\FeaturedGallery;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('pages.gallery', [
            'galleries' => FeaturedGallery::with('images')->active()->orderBy('sort_order')
                ->orderByDesc('created_at')->orderBy('id')->paginate(12),
        ]);
    }
}
