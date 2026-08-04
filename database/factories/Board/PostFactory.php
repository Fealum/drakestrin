<?php

namespace Database\Factories\Board;

use App\Models\Board\Board;
use App\Models\Board\Post;
use App\Models\Board\Thread;
use App\Models\User;
use App\Models\User\Character;
use App\Support\PostElementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (Post $post) {
            if ($post->elements()->exists()) {
                return;
            }
            $element = $post->elements()->create(['position' => 100, 'type' => PostElementType::MESSAGE]);
            $element->message()->create(['message' => (string) $post->message, 'smilies' => (bool) $post->smilies]);
        });
    }

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
