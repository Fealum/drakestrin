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
            $element->message()->create(['message' => fake()->paragraph(), 'smilies' => true]);
        });
    }

    public function withMessage(string $message, bool $smilies = true): static
    {
        return $this->afterCreating(fn (Post $post) => $post->messages()->update([
            'message' => $message,
            'smilies' => $smilies,
        ]));
    }

    public function definition(): array
    {
        return [
            'board_id' => Board::factory(),
            'thread_id' => Thread::factory(),
            'user_id' => User::factory(),
            'character_id' => Character::factory(),
            'time' => now(),
            'ip' => fake()->ipv4(),
        ];
    }
}
