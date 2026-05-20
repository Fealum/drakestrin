<?php

namespace Database\Factories\Board;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board\Board>
 */
class BoardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => 0,
            'name' => fake()->unique()->words(3, true),
            'password' => '',
            'description' => '',
            'thread_count' => 0,
            'post_count' => 0,
            'last_post_at' => 0,
            'last_post_id' => 0,
            'sort' => 0,
            'cat' => false,
            'invisible' => false,
            'style_set' => 0,
            'countposts' => 1,
            'board_extragroups' => '',
            'hp' => 0,
        ];
    }

    public function category(): static
    {
        return $this->state(fn (array $attributes) => [
            'cat' => true,
        ]);
    }
}
