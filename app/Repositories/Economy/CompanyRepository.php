<?php

namespace App\Repositories\Economy;

use App\Data\Economy\CompanyData;
use App\Models\Economy\Company;
use App\Models\Economy\CompanySite;
use App\Models\Territory\Location;
use App\Models\Territory\Settlement;
use App\Models\Territory\Territory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanyRepository
{
    public function create(CompanyData $data, User $creator): Company
    {
        return DB::transaction(function () use ($data, $creator) {
            $location = Location::findOrFail($data->locationId);
            $company = Company::create([
                'name' => $data->name,
                'type' => $data->sector,
                'character_id' => $data->ownerCharacterId,
                'created_by_user_id' => $creator->id,
                'description' => $data->description,
                'text' => $data->text,
                'territory_id' => $this->legacyTerritoryId($location),
                'thread_id' => 0,
                'url' => $data->url,
                'volksgeld' => 0,
            ]);

            $company->sites()->create([
                'location_id' => $location->id,
                'is_headquarters' => true,
                'is_storefront' => $data->isStorefront,
            ]);

            return $company;
        });
    }

    public function update(Company $company, CompanyData $data): Company
    {
        return DB::transaction(function () use ($company, $data) {
            $location = Location::findOrFail($data->locationId);
            $company->update([
                'name' => $data->name,
                'type' => $data->sector,
                'description' => $data->description,
                'text' => $data->text,
                'territory_id' => $this->legacyTerritoryId($location),
                'url' => $data->url,
            ]);

            $headquarters = $company->headquarters()->first();

            if ($headquarters) {
                $duplicate = $company->sites()
                    ->where('location_id', $location->id)
                    ->whereKeyNot($headquarters->id)
                    ->first();

                if ($duplicate) {
                    $duplicate->update([
                        'is_headquarters' => true,
                        'is_storefront' => $data->isStorefront || $duplicate->is_storefront,
                    ]);
                    $headquarters->delete();
                } else {
                    $headquarters->update([
                        'location_id' => $location->id,
                        'is_storefront' => $data->isStorefront,
                    ]);
                }
            } else {
                CompanySite::create([
                    'company_id' => $company->id,
                    'location_id' => $location->id,
                    'is_headquarters' => true,
                    'is_storefront' => $data->isStorefront,
                ]);
            }

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
