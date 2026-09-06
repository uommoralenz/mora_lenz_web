<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'pillar_or_panel' => fake()->randomElement(['Event Panel', 'Photography Pillar', 'Creative Design Pillar']),
            'position' => fake()->jobTitle(),
            'bio' => fake()->paragraph(),
            'photo_url' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?w=300&h=300&fit=crop&crop=faces&auto=format&q=60',
            'card_size' => 'sm',
            'sort_order' => fake()->numberBetween(1, 40),
        ];
    }
}
