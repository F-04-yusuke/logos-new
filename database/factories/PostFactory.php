<?php

namespace Database\Factories;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'topic_id'      => Topic::factory(),
            'url'           => fake()->url(),
            'category'      => 'Article',
            'comment'       => fake()->sentence(),
            'title'         => fake()->sentence(),
            'thumbnail_url' => null,
        ];
    }
}
