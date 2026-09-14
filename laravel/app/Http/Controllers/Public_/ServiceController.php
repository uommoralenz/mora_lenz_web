<?php

namespace App\Http\Controllers\Public_;

use App\Models\ServiceImage;
use App\Models\ServicePackage;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends Controller
{
    /** Copy for the two service pages, mirroring the original site. */
    public const SERVICES = [
        'photography' => [
            'title' => 'Photography Services',
            'description' => 'Capturing moments that last a lifetime. Our professional photography team specializes in event coverage, portraits, and creative shoots.',
            'icon' => 'camera',
            'features' => [
                'Event Coverage',
                'Portrait Sessions',
                'Product Photography',
                'Creative Direction',
            ],
        ],
        'videography' => [
            'title' => 'Videography Services',
            'description' => 'Telling your story through motion. High-quality video production for events, promotional content, and creative storytelling.',
            'icon' => 'video',
            'features' => [
                'Event Highlights',
                'Commercial Shoots',
                'Documentary Style',
                'Post-Production & Editing',
            ],
        ],
    ];

    public function show(string $type): View
    {
        if (! array_key_exists($type, self::SERVICES)) {
            throw new NotFoundHttpException('Unknown service.');
        }

        return view('pages.service', [
            'type' => $type,
            'service' => self::SERVICES[$type],
            'packages' => ServicePackage::active()
                ->ofType($type)
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->get(),
            'carousel' => ServiceImage::active()
                ->ofType($type)
                ->orderBy('sort_order')
                ->pluck('image_url')
                ->all(),
        ]);
    }
}
