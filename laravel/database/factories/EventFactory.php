<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('-1 month', '+3 months'),
            'location' => fake()->city(),
            'image_urls' => ['/events/past/media-awards-2025.webp'],
            'status' => fake()->randomElement(['upcoming', 'past']),
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
