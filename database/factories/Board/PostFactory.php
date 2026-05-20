<?php

namespace Database\Factories\Board;

use App\Models\Board\Board;
use App\Models\Board\Thread;
use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'board_id' => Board::factory(),
            'thread_id' => Thread::factory(),
            'user_id' => User::factory(),
            'character_id' => Character::factory(),
            'time' => now(),
            'message' => fake()->paragraph(),
            'smilies' => true,
            'signature' => false,
            'ip' => fake()->ipv4(),
        ];
    }
}
