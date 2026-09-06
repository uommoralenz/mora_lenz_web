<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Member;
use App\Models\PanelPillar;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $members = Member::query()
            ->orderBy('sort_order')
            ->get();

        $executiveMembers = $members
            ->where('card_size', '!=', 'lg')
            ->values();

        return view('home', [
            'features' => $this->features(),
            'galleryImages' => $this->galleryImages(),
            'upcomingEvents' => Event::query()
                ->upcoming()
                ->orderBy('sort_order')
                ->orderBy('event_date')
                ->get(),
            'pastEvents' => Event::query()
                ->past()
                ->orderBy('sort_order')
                ->orderByDesc('event_date')
                ->get(),
            'advisors' => $members->where('card_size', 'lg')->values(),
            'executiveRows' => collect([
                ['cols' => 2, 'members' => $executiveMembers->slice(0, 2)->values()],
                ['cols' => 2, 'members' => $executiveMembers->slice(2, 2)->values()],
                ['cols' => 4, 'members' => $executiveMembers->slice(4, 4)->values()],
                ['cols' => 4, 'members' => $executiveMembers->slice(8, 4)->values()],
                ['cols' => 4, 'members' => $executiveMembers->slice(12, 4)->values()],
                ['cols' => 3, 'members' => $executiveMembers->slice(16, 3)->values()],
            ]),
            'panelsPillars' => $this->panelsPillars(),
            'ads' => $this->ads(),
        ]);
    }

    /**
     * @return array<int, array{icon: string, title: string, description: string}>
     */
    private function features(): array
    {
        return [
            [
                'icon' => 'camera',
                'title' => 'Photography',
                'description' => 'Professional photography training and equipment access',
            ],
            [
                'icon' => 'video',
                'title' => 'Videography',
                'description' => 'Learn film production and video editing techniques',
            ],
            [
                'icon' => 'users',
                'title' => 'Community',
                'description' => 'Join a vibrant community of creative minds',
            ],
            [
                'icon' => 'award',
                'title' => 'Events',
                'description' => 'Participate in exhibitions and competitions',
            ],
        ];
    }

    /**
     * @return array<int, array{url: string, title: string, category: string}>
     */
    private function galleryImages(): array
    {
        return GalleryImage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn (GalleryImage $image): array => [
                'url' => $image->image_url,
                'title' => $image->title,
                'category' => $image->category ?? 'Gallery',
            ])
            ->all();
    }

    /**
     * @return array<int, array{icon: string, name: string, description: string, members: int}>
     */
    private function panelsPillars(): array
    {
        return PanelPillar::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (PanelPillar $item): array => [
                'icon' => $item->icon,
                'name' => $item->name,
                'description' => $item->description ?? '',
                'members' => $item->member_count,
            ])
            ->all();
    }

    /**
     * @return array<int, array{icon: string, title: string, subtitle: string, date: string, color: string}>
     */
    private function ads(): array
    {
        return [
            [
                'icon' => 'calendar',
                'title' => 'Upcoming Workshop',
                'subtitle' => 'Advanced Portrait Photography',
                'date' => 'Feb 15, 2026',
                'color' => 'from-purple-600 to-pink-600',
            ],
            [
                'icon' => 'trophy',
                'title' => 'Annual Competition',
                'subtitle' => 'Best Media Project 2026',
                'date' => 'Deadline: Mar 1',
                'color' => 'from-blue-600 to-cyan-600',
            ],
            [
                'icon' => 'book-open',
                'title' => 'New Course',
                'subtitle' => 'Cinematic Video Editing',
                'date' => 'Starting Soon',
                'color' => 'from-green-600 to-teal-600',
            ],
        ];
    }
}
