<?php

namespace Database\Factories\Economy;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Economy\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'stackable' => 1,
            'description' => '',
            'img' => 1,
            'weight' => 1,
            'unit' => '',
        ];
    }

    public function tool(): static
    {
        return $this->state(fn (array $attributes) => [
            'stackable' => 0,
        ]);
    }
}
