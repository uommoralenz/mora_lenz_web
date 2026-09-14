<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\FeaturedGallery;
use App\Models\ServicePackage;
use App\Models\TeamGroup;
use App\Models\TeamMember;
use App\Models\TeamSubgroup;
use Illuminate\Database\Seeder;

/**
 * Starter content so a fresh install is not an empty black page.
 *
 * Skips anything that already has rows, so it is safe to re-run and will never
 * overwrite real content added from the admin panel.
 */
class SampleContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEvents();
        $this->seedGalleries();
        $this->seedPackages();
        $this->seedTeam();
    }

    protected function seedEvents(): void
    {
        if (Event::exists()) {
            return;
        }

        Event::create([
            'title' => 'Annual Photo Walk',
            'description' => "A guided walk through Colombo's old quarter with the club's senior photographers.\n\nBring any camera you own — phones included. We cover composition, available light, and street etiquette along the way, then review everyone's frames together over tea at the end.",
            'event_date' => now()->addDays(21)->setTime(7, 30),
            'location' => 'Pettah, Colombo',
            'countdown_enabled' => true,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Event::create([
            'title' => 'Lighting Workshop: Studio Basics',
            'description' => "A hands-on session on one-light and two-light portrait setups, run in the university studio.\n\nLimited to twenty participants so everyone gets time behind the camera.",
            'event_date' => now()->addDays(45)->setTime(14, 0),
            'location' => 'Media Unit Studio, University of Moratuwa',
            'countdown_enabled' => false,
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    protected function seedGalleries(): void
    {
        if (FeaturedGallery::exists()) {
            return;
        }

        // image_url is left as a placeholder path — replace these from the
        // admin panel, which will upload real files into public/uploads.
        $items = [
            ['Convocation 2025', 'Three days, eleven photographers, and the widest smiles on campus. A record of the graduating batch as they crossed the stage.'],
            ['Mora Sports Meet', 'Track, field and everything in between, shot from the inside lane. Motion, grain and the noise of a full stadium.'],
            ['Campus After Dark', 'A long-exposure series on the quieter side of the university — empty corridors, sodium light and the walk home.'],
        ];

        foreach ($items as $index => [$title, $description]) {
            FeaturedGallery::create([
                'title' => $title,
                'description' => $description,
                'image_url' => '',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }

    protected function seedPackages(): void
    {
        if (ServicePackage::exists()) {
            return;
        }

        ServicePackage::create([
            'service_type' => 'photography',
            'name' => 'Event Coverage — Half Day',
            'description' => [
                'Up to 4 hours of coverage',
                'Two photographers on site',
                '150+ edited high-resolution images',
                'Online gallery delivered within 7 days',
            ],
            'price' => 25000,
            'offered_price' => 19500,
            'image_url' => '',
            'sort_order' => 1,
        ]);

        ServicePackage::create([
            'service_type' => 'photography',
            'name' => 'Portrait Session',
            'description' => [
                'One hour studio or outdoor session',
                '20 retouched images',
                'Two outfit changes',
                'Print-ready files included',
            ],
            'price' => 8000,
            'image_url' => '',
            'sort_order' => 2,
        ]);

        ServicePackage::create([
            'service_type' => 'videography',
            'name' => 'Event Highlight Film',
            'description' => [
                'Full-day coverage',
                '3–5 minute edited highlight film',
                'Licensed music and colour grading',
                'Delivered in 4K and social cuts',
            ],
            'price' => 45000,
            'image_url' => '',
            'sort_order' => 1,
        ]);
    }

    protected function seedTeam(): void
    {
        if (TeamGroup::exists()) {
            return;
        }

        $advisors = TeamGroup::create([
            'name' => 'Advisors & Senior Leadership',
            'description' => 'The people who keep the club pointed in the right direction.',
            'sort_order' => 1,
        ]);

        TeamMember::create([
            'group_id' => $advisors->id,
            'name' => 'Senior Treasurer',
            'profession' => 'Faculty Advisor',
            'description' => 'Guides the club on university policy and finance.',
            'sort_order' => 1,
        ]);

        $exco = TeamGroup::create([
            'name' => 'Executive Committee',
            'description' => 'General Members',
            'sort_order' => 2,
        ]);

        $board = TeamSubgroup::create([
            'group_id' => $exco->id,
            'name' => 'Board',
            'sort_order' => 1,
        ]);

        foreach ([['President', 1], ['Secretary', 2], ['Treasurer', 3]] as [$role, $order]) {
            TeamMember::create([
                'group_id' => $exco->id,
                'subgroup_id' => $board->id,
                'name' => $role,
                'profession' => $role,
                'sort_order' => $order,
            ]);
        }

        $crew = TeamSubgroup::create([
            'group_id' => $exco->id,
            'name' => 'Photography Crew',
            'sort_order' => 2,
        ]);

        foreach ([['Lead Photographer', 1], ['Lead Videographer', 2]] as [$role, $order]) {
            TeamMember::create([
                'group_id' => $exco->id,
                'subgroup_id' => $crew->id,
                'name' => $role,
                'profession' => $role,
                'sort_order' => $order,
            ]);
        }
    }
}
