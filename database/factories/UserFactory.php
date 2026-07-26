<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timestamp = now();

        return [
            'name' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'regemail' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'avatar_character_id' => 0,
            'usertext' => '',
            'birthday' => 0,
            'interests' => '',
            'location' => '',
            'work' => '',
            'gender' => 0,
            'post_count' => 0,
            'regdate' => $timestamp,
            'lastvisit' => $timestamp,
            'lastactivity' => $timestamp,
            'wohnort' => '',
            'remember_token' => Str::random(10),
        ];
    }

}
