<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function view(?User $user, Company $company): bool
    {
        return true;
    }

    public function manage(User $user, Company $company): bool
    {
        return $this->owns($user, $company);
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
        $company->loadMissing('characterModel');

        return (int) $company->characterModel?->user === (int) $user->id;
    }
}
