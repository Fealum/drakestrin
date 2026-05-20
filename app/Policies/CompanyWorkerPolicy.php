<?php

namespace App\Policies;

use App\Models\Economy\CompanyWorker;
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
        $worker->loadMissing('company.character');

        return (int) $worker->company?->character?->user_id === (int) $user->id;
    }
}
