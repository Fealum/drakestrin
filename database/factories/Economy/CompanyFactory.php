<?php

namespace Database\Factories\Economy;

use App\Models\User\Character;
use App\Models\Territory\Territory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Economy\Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'type' => 2,
            'character_id' => Character::factory(),
            'description' => '',
            'text' => '',
            'territory_id' => Territory::factory(),
            'thread_id' => 0,
            'url' => '',
            'volksgeld' => 0,
        ];
    }
}
