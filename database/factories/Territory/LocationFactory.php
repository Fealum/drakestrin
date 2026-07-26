<?php

namespace Database\Factories\Territory;

use App\Models\Territory\Territory;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Territory\Location>
 */
class LocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_type' => PermissionEntityType::TERRITORY->value,
            'parent_id' => Territory::factory(),
            'created_by_user_id' => null,
            'name' => fake()->unique()->streetName(),
            'description' => fake()->sentence(),
            'priority' => 0,
        ];
    }
}
