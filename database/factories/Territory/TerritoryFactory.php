<?php

namespace Database\Factories\Territory;

use App\Models\Territory\Settlement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Territory\Territory>
 */
class TerritoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city(),
            'type' => '2',
            'parent_id' => 0,
            'character_id' => 0,
            'area' => fake()->numberBetween(1000000, 100000000),
            'population' => fake()->numberBetween(100, 100000),
            'geldstand' => 0,
            'beliebtheit' => 50,
            'capital_id' => Settlement::factory(),
        ];
    }
}
