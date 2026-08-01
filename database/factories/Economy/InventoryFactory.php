<?php

namespace Database\Factories\Economy;

use App\Models\Economy\Inventory;
use App\Models\Economy\Item;
use App\Models\User\Character;
use App\Support\PermissionEntityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inventory>
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

    public function companySite(int $siteId): static
    {
        return $this->state(fn (array $attributes): array => [
            'owner_id' => $siteId,
            'owner_type' => PermissionEntityType::COMPANY_SITE->value,
        ]);
    }
}
