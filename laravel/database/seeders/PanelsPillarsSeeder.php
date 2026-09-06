<?php

namespace Database\Seeders;

use App\Models\PanelPillar;
use Illuminate\Database\Seeder;

class PanelsPillarsSeeder extends Seeder
{
    /**
     * Seed the application's panels and pillars.
     */
    public function run(): void
    {
        $items = [
            ['type' => 'panel', 'icon' => 'users', 'name' => 'Event Panel', 'description' => 'Manage event planning and execution', 'member_count' => 28, 'sort_order' => 1],
            ['type' => 'panel', 'icon' => 'dollar-sign', 'name' => 'Financial Panel', 'description' => 'Manage budgets and financial planning', 'member_count' => 15, 'sort_order' => 2],
            ['type' => 'panel', 'icon' => 'user-check', 'name' => 'HR Panel', 'description' => 'Manage recruitment and member relations', 'member_count' => 12, 'sort_order' => 3],
            ['type' => 'panel', 'icon' => 'message-circle', 'name' => 'PR Panel', 'description' => 'Manage public relations and media outreach', 'member_count' => 10, 'sort_order' => 4],
            ['type' => 'panel', 'icon' => 'file-text', 'name' => 'Secretarial Panel', 'description' => 'Manage documentation and administrative tasks', 'member_count' => 7, 'sort_order' => 5],
            ['type' => 'pillar', 'icon' => 'camera', 'name' => 'Photography Pillar', 'description' => 'Capture stunning visuals and moments', 'member_count' => 37, 'sort_order' => 6],
            ['type' => 'pillar', 'icon' => 'video', 'name' => 'Videography Pillar', 'description' => 'Create compelling video content', 'member_count' => 26, 'sort_order' => 7],
            ['type' => 'pillar', 'icon' => 'pen-tool', 'name' => 'Creative Design Pillar', 'description' => 'Visual identity and graphic design', 'member_count' => 22, 'sort_order' => 8],
            ['type' => 'pillar', 'icon' => 'pen-tool', 'name' => 'Editorial Pillar', 'description' => 'Content creation and editorial processes', 'member_count' => 16, 'sort_order' => 9],
            ['type' => 'pillar', 'icon' => 'mic', 'name' => 'Announcing Pillar', 'description' => 'Manage event hosting and public speaking initiatives', 'member_count' => 94, 'sort_order' => 10],
        ];

        foreach ($items as $item) {
            PanelPillar::query()->updateOrCreate([
                'name' => $item['name'],
            ], $item);
        }
    }
}
