<?php

namespace App\Repositories\Economy;

use App\Data\Economy\CompanyData;
use App\Models\Economy\Company;
use App\Models\Territory\Location;
use App\Models\Territory\Settlement;
use App\Models\Territory\Territory;
use App\Models\User;
use App\Support\PermissionEntityType;
use Illuminate\Support\Facades\DB;

class CompanyRepository
{
    public function create(CompanyData $data, User $creator): Company
    {
        return DB::transaction(function () use ($data, $creator) {
            $location = $data->locationId
                ? Location::findOrFail($data->locationId)
                : Location::create([
                    'parent_type' => PermissionEntityType::TERRITORY->value,
                    'parent_id' => Territory::query()->whereKey($data->newLocationParentTerritoryId)->where('type', 5)->value('id'),
                    'created_by_user_id' => $creator->id,
                    'name' => $data->name,
                    'description' => null,
                    'priority' => 0,
                ]);
            $company = Company::create([
                'name' => $data->name,
                'type' => $data->sector,
                'created_by_user_id' => $creator->id,
                'description' => $data->description,
                'territory_id' => $this->legacyTerritoryId($location),
                'thread_id' => 0,
                'volksgeld' => 0,
            ]);

            $company->owners()->create([
                'character_id' => $data->ownerCharacterId,
                'added_by_user_id' => $creator->id,
            ]);
            DB::table('company_role_events')->insert([
                'company_id' => $company->id,
                'company_site_id' => null,
                'character_id' => $data->ownerCharacterId,
                'role' => 'owner',
                'action' => 'appointed',
                'acted_by_user_id' => $creator->id,
                'created_at' => now(),
            ]);
            $site = $company->sites()->create([
                'location_id' => $location->id,
                'name' => 'Hauptstandort',
            ]);
            $company->update(['headquarters_site_id' => $site->id]);

            return $company->refresh();
        });
    }

    public function update(Company $company, CompanyData $data): Company
    {
        return DB::transaction(function () use ($company, $data) {
            $company->update([
                'name' => $data->name,
                'type' => $data->sector,
                'description' => $data->description,
            ]);

            return $company->refresh();
        });
    }

    private function legacyTerritoryId(Location $location): int
    {
        $parent = $location->parent;

        while ($parent instanceof Location) {
            $parent = $parent->parent;
        }

        if ($parent instanceof Territory) {
            return (int) $parent->id;
        }

        if ($parent instanceof Settlement) {
            $territoryId = $parent->territories()->value('id');

            if ($territoryId) {
                return (int) $territoryId;
            }
        }

        return (int) Territory::query()->whereDoesntHave('children')->value('id');
    }
}
