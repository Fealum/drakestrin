<?php

namespace Database\Factories\Board;

use App\Models\Board\Thread;
use App\Models\Territory\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board\ThreadScene>
 */
class ThreadSceneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'thread_id' => Thread::factory(),
            'location_id' => Location::factory(),
            'starts_at_post_id' => null,
            'ends_at_post_id' => null,
            'story_started_at' => now()->timestamp,
            'story_ended_at' => null,
            'ended_at' => null,
            'created_by_user_id' => null,
        ];
    }
}
