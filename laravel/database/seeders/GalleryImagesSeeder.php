<?php

namespace Database\Seeders;

use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

class GalleryImagesSeeder extends Seeder
{
    /**
     * Seed the application's gallery images.
     */
    public function run(): void
    {
        $images = [
            ['image_url' => 'https://images.unsplash.com/photo-1758270703733-3663d99c9dd7?w=1200', 'title' => 'Photography Workshop', 'category' => 'Events', 'sort_order' => 1],
            ['image_url' => 'https://images.unsplash.com/photo-1758901466295-a3a67cac9021?w=1200', 'title' => 'Film Production', 'category' => 'Projects', 'sort_order' => 2],
            ['image_url' => 'https://images.unsplash.com/photo-1759784120360-8b5044b71f47?w=1200', 'title' => 'Studio Sessions', 'category' => 'Behind The Scenes', 'sort_order' => 3],
            ['image_url' => 'https://images.unsplash.com/photo-1764254812010-78ab61ef114b?w=1200', 'title' => 'Campus Events', 'category' => 'Coverage', 'sort_order' => 4],
            ['image_url' => 'https://images.unsplash.com/photo-1762356121454-877acbd554bb?w=1200', 'title' => 'Film Festival', 'category' => 'Events', 'sort_order' => 5],
            ['image_url' => 'https://images.unsplash.com/photo-1735639013995-086e648eaa38?w=1200', 'title' => 'Team Collaboration', 'category' => 'Workshops', 'sort_order' => 6],
            ['image_url' => 'https://images.unsplash.com/photo-1674668920910-85b8d3c187ca?w=1200', 'title' => 'Equipment Training', 'category' => 'Training', 'sort_order' => 7],
            ['image_url' => 'https://images.unsplash.com/photo-1742497359858-8e0a442c9c55?w=1200', 'title' => 'Photo Exhibition', 'category' => 'Exhibitions', 'sort_order' => 8],
        ];

        foreach ($images as $image) {
            GalleryImage::query()->updateOrCreate([
                'title' => $image['title'],
            ], array_merge(['is_active' => true], $image));
        }
    }
}
