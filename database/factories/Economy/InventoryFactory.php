<?php

namespace Database\Factories\Economy;

use App\Models\User\Character;
use App\Models\Economy\Item;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Economy\Inventory>
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
            'owner_type' => PermissionEntityType::CHARACTER->value,
            'timelastvalue' => 0,
            'data' => '',
        ];
    }

    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_type' => PermissionEntityType::COMPANY->value,
        ]);
    }
}
