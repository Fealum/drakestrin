<?php

namespace App\Policies;

use App\Models\Economy\Company;
use App\Models\User;
use App\Models\User\Character;
use App\Services\PermissionService;

class CompanyPolicy
{
    public function __construct(private PermissionService $permissions) {}

    public function view(?User $user, Company $company): bool
    {
        return true;
    }

    public function manage(User $user, Company $company): bool
    {
        return $this->owns($user, $company) || $company->representatives()
            ->whereHas('character', fn ($query) => $query->where('user_id', $user->id))
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->characters()->exists() && $this->permissions->allows('createcompany', null, $user);
    }

    public function update(User $user, Company $company): bool
    {
        return $this->owns($user, $company)
            && $this->permissions->allows('editcompany', $company, $user);
    }

    public function manageRepresentatives(User $user, Company $company): bool
    {
        return $this->owns($user, $company);
    }

    public function represent(User $user, Company $company, Character $character): bool
    {
        return (int) $character->user_id === (int) $user->id
            && $company->isRepresentedBy($character)
            && $this->permissions->allows('representcompany', $company, $user);
    }

    public function hire(User $user, Company $company): bool
    {
        return $this->manage($user, $company);
    }

    public function pay(User $user, Company $company): bool
    {
        return $this->manage($user, $company);
    }

    private function owns(User $user, Company $company): bool
    {
        $company->loadMissing('character');

        return (int) $company->character?->user_id === (int) $user->id;
    }
}
