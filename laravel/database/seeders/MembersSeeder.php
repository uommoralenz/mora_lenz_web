<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MembersSeeder extends Seeder
{
    /**
     * Seed the application's members.
     */
    public function run(): void
    {
        $placeholder = 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60';

        $members = [
            [
                'first_name' => 'Dr. Thesara',
                'last_name' => 'Jayawardena',
                'pillar_or_panel' => 'Advisory',
                'position' => 'Senior Treasurer & Advisor of Mora Lenz Media Club',
                'bio' => 'I am deeply committed to nurturing the creative talents within Mora Lenz Media Club. Through strategic financial guidance and mentorship, I believe we can empower our members to achieve excellence in media arts while building a sustainable foundation for future generations.',
                'photo_url' => '/Thesara-madam.webp',
                'card_size' => 'lg',
                'sort_order' => 1,
            ],
            ['position' => 'President', 'bio' => 'Leading Mora Lenz with vision and dedication, overseeing all club activities and strategic initiatives.'],
            ['position' => 'Vice President', 'bio' => 'Supporting club leadership and managing day-to-day operations to ensure smooth functioning of all activities.'],
            ['position' => 'Secretary', 'bio' => 'Managing club documentation, communications, and ensuring proper record-keeping of all official matters.'],
            ['position' => 'Assistant Secretary', 'bio' => 'Supporting secretarial duties and helping maintain organized communication channels within the club.'],
            ['position' => 'Junior Treasurer', 'bio' => 'Managing club finances, budgets, and ensuring transparent financial operations for all activities.'],
            ['position' => 'Assistant Junior Treasurer', 'bio' => 'Supporting financial management and helping maintain accurate financial records and transactions.'],
            ['position' => 'Chief Coordinator', 'bio' => 'Orchestrating major events and ensuring seamless coordination between different club departments.'],
            ['position' => 'Chief Coordinator', 'bio' => 'Leading coordination efforts and managing cross-departmental collaboration for successful project execution.'],
            ['position' => 'Event Coordinator & Technical Lead', 'bio' => 'Combining event management expertise with technical skills to deliver outstanding multimedia experiences.'],
            ['position' => 'Event Coordinator', 'bio' => 'Planning and executing engaging events that showcase the creative talents of our club members.'],
            ['position' => 'Photography Pillar Head', 'bio' => 'Leading the photography team with expertise in capturing stunning visuals and mentoring emerging photographers.'],
            ['position' => 'Assistant Photography Pillar Head', 'bio' => 'Supporting photography initiatives and helping develop technical skills within the photography department.'],
            ['position' => 'Creative Design Pillar Head', 'bio' => 'Directing creative design projects and maintaining the visual identity of Mora Lenz across all platforms.'],
            ['position' => 'Videography Pillar Head', 'bio' => 'Leading video production initiatives and creating compelling visual narratives for club documentation.'],
            ['position' => 'Assistant Videography Pillar Head', 'bio' => 'Supporting video production and helping coordinate filming activities for various club events.'],
            ['position' => 'Editorial Pillar Head', 'bio' => 'Overseeing content creation and editorial processes to maintain high-quality club publications.'],
            ['position' => 'Assistant Editorial Pillar Head', 'bio' => 'Supporting editorial work and helping curate engaging content for club communications.'],
            ['position' => 'Announcing Pillar Head', 'bio' => 'Leading event hosting and public speaking initiatives, bringing energy and professionalism to club events.'],
            ['position' => 'Assistant Announcing Pillar Head', 'bio' => 'Supporting announcement activities and helping develop presentation skills within the club.'],
        ];

        foreach ($members as $index => $member) {
            $data = array_merge([
                'first_name' => 'Osada',
                'last_name' => 'Bimsara',
                'pillar_or_panel' => null,
                'photo_url' => $placeholder,
                'card_size' => 'sm',
                'sort_order' => $index + 1,
            ], $member);

            Member::query()->updateOrCreate([
                'position' => $data['position'],
                'sort_order' => $data['sort_order'],
            ], $data);
        }
    }
}
