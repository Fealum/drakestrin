<?php

namespace App\Policies;

use App\Models\CompanyWorker;
use App\Models\User;

class CompanyWorkerPolicy
{
    public function view(?User $user, CompanyWorker $worker): bool
    {
        return true;
    }

    public function manage(User $user, CompanyWorker $worker): bool
    {
        return $this->owns($user, $worker);
    }

    public function fire(User $user, CompanyWorker $worker): bool
    {
        return $this->manage($user, $worker);
    }

    public function assignLabour(User $user, CompanyWorker $worker): bool
    {
        return $this->manage($user, $worker);
    }

    private function owns(User $user, CompanyWorker $worker): bool
    {
        $worker->loadMissing('companyModel.characterModel');

        return (int) $worker->companyModel?->characterModel?->user === (int) $user->id;
    }
}
