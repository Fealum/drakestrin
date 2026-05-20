<?php

namespace Database\Factories\Board;

use App\Models\Board\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board\Thread>
 */
class ThreadFactory extends Factory
{
    public function definition(): array
    {
        $timestamp = now();

        return [
            'board_id' => Board::factory(),
            'first_post_at' => $timestamp,
            'name' => fake()->sentence(4),
            'post_count' => 0,
            'first_post_id' => 0,
            'last_post_id' => 0,
            'last_post_at' => $timestamp,
            'views' => 0,
            'flags' => false,
            'topicicon' => 0,
            'rate_points' => 0,
            'rated' => 0,
            'putoffid' => 0,
            'important' => false,
            'pquestion' => '',
            'ptimeout' => 0,
            'rpg' => 0,
            'shopthread' => 0,
            'altercat' => 0,
        ];
    }
}
