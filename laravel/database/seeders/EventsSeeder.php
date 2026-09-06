<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventsSeeder extends Seeder
{
    /**
     * Seed the application's events.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Dummy event',
                'description' => 'Join us for an evening of contemporary art showcasing local artists.',
                'event_date' => '2026-02-15 19:00:00',
                'location' => 'Centra Court',
                'image_urls' => ['/events/upcoming/media-awards-2025.webp'],
                'status' => 'upcoming',
                'sort_order' => 1,
            ],
            [
                'title' => 'Dummy thama itin mekath',
                'description' => 'A hands-on workshop for photography enthusiasts of all levels.',
                'event_date' => '2026-03-20 10:00:00',
                'location' => 'Rubert Peiris Auditorium',
                'image_urls' => ['/events/upcoming/Sandwani-3.webp'],
                'status' => 'upcoming',
                'sort_order' => 2,
            ],
            [
                'title' => 'Media Awards 2025',
                'description' => "Sri Lanka's Biggest media competition of the year organized by Mora Lenz. Celebrating excellence in media and creativity.",
                'event_date' => '2025-12-20 18:00:00',
                'location' => null,
                'image_urls' => ['/events/past/media-awards-2025.webp'],
                'status' => 'past',
                'sort_order' => 1,
            ],
            [
                'title' => 'Sandwani 3.0',
                'description' => 'A musical event organized by MoraLenz engaging with talented individuals in the field of music.',
                'event_date' => '2025-10-10 14:00:00',
                'location' => null,
                'image_urls' => ['/events/past/Sandwani-3.webp'],
                'status' => 'past',
                'sort_order' => 2,
            ],
            [
                'title' => 'dummy event 1',
                'description' => 'The island has a documented history of over 3,000 years, with evidence of prehistoric human settlement dating back 125,000 years.',
                'event_date' => '2025-12-20 18:00:00',
                'location' => null,
                'image_urls' => ['/events/past/media-awards-2025.webp'],
                'status' => 'past',
                'sort_order' => 3,
            ],
            [
                'title' => 'dummy event 2',
                'description' => 'Explorers across the world as early as the Anuradhapura period. The Portuguese Empire established a colony in the sixteenth century, during a period of political change.',
                'event_date' => '2025-12-20 18:00:00',
                'location' => null,
                'image_urls' => ['/events/past/Sandwani-3.webp'],
                'status' => 'past',
                'sort_order' => 4,
            ],
            [
                'title' => 'dummy event 3',
                'description' => 'Explorers across the world as early as the Anuradhapura period. The Portuguese Empire established a colony in the sixteenth century, during a period of political change.',
                'event_date' => '2025-12-20 18:00:00',
                'location' => null,
                'image_urls' => ['/events/past/media-awards-2025.webp'],
                'status' => 'past',
                'sort_order' => 5,
            ],
        ];

        foreach ($events as $event) {
            Event::query()->updateOrCreate([
                'title' => $event['title'],
                'status' => $event['status'],
                'sort_order' => $event['sort_order'],
            ], $event);
        }
    }
}
