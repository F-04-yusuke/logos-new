<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title'   => fake()->sentence(6),
            'content' => fake()->paragraph(3),
        ];
    }
}
