<?php

namespace App\Policies;

use App\Models\Economy\CompanySite;
use App\Models\User;
use App\Models\User\Character;
use App\Support\CompanyRepresentativeRole;

class CompanySitePolicy
{
    public function view(?User $user, CompanySite $site): bool
    {
        return true;
    }

    public function update(User $user, CompanySite $site): bool
    {
        return $site->company?->isManagedByUserId($user->id) ?? false;
    }

    public function delete(User $user, CompanySite $site): bool
    {
        return $this->update($user, $site);
    }

    public function manageInventory(User $user, CompanySite $site): bool
    {
        return $this->hasOperationalRole($user, $site, [CompanyRepresentativeRole::FOREMAN]);
    }

    public function viewInventory(User $user, CompanySite $site): bool
    {
        return $this->hasOperationalRole($user, $site, [
            CompanyRepresentativeRole::FOREMAN,
            CompanyRepresentativeRole::CLERK,
        ]);
    }

    public function manageWorkers(User $user, CompanySite $site): bool
    {
        return $this->hasOperationalRole($user, $site, [CompanyRepresentativeRole::FOREMAN]);
    }

    public function transfer(User $user, CompanySite $site, Character $character): bool
    {
        if ((int) $character->user_id !== (int) $user->id) {
            return false;
        }

        $company = $site->company;

        return $company && (
            $company->owners()->where('character_id', $character->id)->exists()
            || $company->representatives()
                ->where('character_id', $character->id)
                ->where(function ($query) use ($site) {
                    $query->where('role', CompanyRepresentativeRole::MANAGER->value)
                        ->orWhere(function ($query) use ($site) {
                            $query->where('company_site_id', $site->id)
                                ->whereIn('role', [
                                    CompanyRepresentativeRole::FOREMAN->value,
                                    CompanyRepresentativeRole::CLERK->value,
                                ]);
                        });
                })
                ->exists()
        );
    }

    private function hasOperationalRole(User $user, CompanySite $site, array $siteRoles): bool
    {
        $company = $site->company;

        return $company && (
            $company->isManagedByUserId($user->id)
            || $company->representatives()
                ->where('company_site_id', $site->id)
                ->whereIn('role', array_map(fn ($role) => $role->value, $siteRoles))
                ->whereHas('character', fn ($query) => $query->where('user_id', $user->id))
                ->exists()
        );
    }
}
