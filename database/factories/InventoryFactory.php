<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'stack' => 1,
            'wear' => 0,
            'owner_id' => Character::factory(),
            'owner_type' => 6,
            'timelastvalue' => 0,
            'data' => '',
        ];
    }

    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_type' => 2,
        ]);
    }
}
