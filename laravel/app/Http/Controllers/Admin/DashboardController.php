<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\EventPhoto;
use App\Models\ServicePackage;
use App\Models\TeamGroup;
use App\Models\TeamMember;
use App\Models\TeamTerm;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Support\AdminAuth;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $admin = AdminAuth::current();
        $can = fn (string $p) => $admin?->hasPermission($p) ?? false;

        // Only count what this admin is allowed to open.
        return response()->json([
            'data' => [
                'events' => $can('events') ? Event::count() : 0,
                'events_active' => $can('events') ? Event::where('is_active', true)->count() : 0,
                'events_upcoming' => $can('events')
                    ? Event::where('is_active', true)->where('event_date', '>=', now())->count()
                    : 0,
                'events_past' => $can('events')
                    ? Event::where('is_active', true)->whereRaw('COALESCE(end_date, event_date) < ?', [now()])->count()
                    : 0,
                'event_photos' => $can('events') ? EventPhoto::count() : 0,
                'service_packages' => $can('services') ? ServicePackage::count() : 0,
                // Counts for the current team only; past years are history.
                'team_groups' => $can('team') ? TeamGroup::where('term_id', TeamTerm::current()?->id)->count() : 0,
                'team_members' => $can('team')
                    ? TeamMember::whereIn('group_id', TeamGroup::where('term_id', TeamTerm::current()?->id)->select('id'))->count()
                    : 0,
                'team_terms' => $can('team') ? TeamTerm::count() : 0,
                'admins' => $admin?->isSuperAdmin() ? Admin::count() : 0,
                'messages' => $can('messages') ? ContactMessage::count() : 0,
                'messages_unread' => $can('messages') ? ContactMessage::where('is_read', false)->count() : 0,
            ],
            'featured_event' => $can('events')
                ? Event::where('is_featured', true)
                    ->where('is_active', true)
                    ->orderBy('event_date')
                    ->value('title')
                : null,
        ]);
    }
}
