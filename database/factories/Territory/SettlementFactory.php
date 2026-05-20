<?php

namespace Database\Factories\Territory;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Territory\Settlement>
 */
class SettlementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'population' => fake()->numberBetween(100, 10000),
            'priority' => 1,
        ];
    }
}
