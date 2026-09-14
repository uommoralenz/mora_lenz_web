<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\FeaturedGallery;
use App\Models\ServicePackage;
use App\Models\TeamGroup;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'events' => Event::count(),
                'events_active' => Event::where('is_active', true)->count(),
                'events_upcoming' => Event::where('is_active', true)
                    ->where('event_date', '>=', now())
                    ->count(),
                'galleries' => FeaturedGallery::count(),
                'service_packages' => ServicePackage::count(),
                'team_groups' => TeamGroup::count(),
                'team_members' => TeamMember::count(),
                'admins' => Admin::count(),
                'messages' => ContactMessage::count(),
                'messages_unread' => ContactMessage::where('is_read', false)->count(),
            ],
            'featured_event' => Event::where('is_featured', true)
                ->where('is_active', true)
                ->orderBy('event_date')
                ->value('title'),
        ]);
    }
}
