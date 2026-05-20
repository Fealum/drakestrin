<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Character>
 */
class CharacterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->userName(),
            'regdate' => now(),
            'user_id' => User::factory(),
            'usertext' => '',
            'birthday' => 0,
            'avatar' => 0,
            'interests' => '',
            'location' => '',
            'post_count' => 0,
            'work' => '',
            'gender' => 0,
        ];
    }
}
