<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalysisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'topic_id'     => null,
            'title'        => fake()->sentence(4),
            'type'         => fake()->randomElement(['tree', 'matrix', 'swot']),
            'data'         => ['nodes' => []],
            'is_published' => false,
        ];
    }
}
