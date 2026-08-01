<?php

namespace Database\Factories\Economy;

use App\Models\Economy\Company;
use App\Models\Territory\Location;
use App\Models\Territory\Territory;
use App\Models\User\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterCreating(function (Company $company) {
            $character = Character::factory()->create();
            $location = Location::factory()->create();
            $company->owners()->create(['character_id' => $character->id]);
            $site = $company->sites()->create([
                'location_id' => $location->id,
                'name' => 'Hauptstandort',
            ]);
            $company->update(['headquarters_site_id' => $site->id]);
        });
    }

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'type' => 2,
            'description' => '',
            'territory_id' => Territory::factory(),
            'thread_id' => 0,
            'volksgeld' => 0,
        ];
    }
}
