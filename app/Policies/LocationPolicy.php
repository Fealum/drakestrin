<?php

namespace App\Policies;

use App\Models\Territory\Location;
use App\Models\Territory\Settlement;
use App\Models\Territory\Territory;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Database\Eloquent\Model;

class LocationPolicy
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function view(?User $user, Location $location): bool
    {
        return true;
    }

    public function create(User $user, ?Model $parent = null): bool
    {
        return $this->canHostLocations($parent)
            && $this->permissions->allows('createlocation', $parent, $user);
    }

    public function update(User $user, Location $location): bool
    {
        return $this->permissions->allowsOwn('editlocation', $location, $location->created_by_user_id, $user);
    }

    public function delete(User $user, Location $location): bool
    {
        return $this->permissions->check('deletelocation', $location, $user) === 2;
    }

    private function canHostLocations(?Model $parent): bool
    {
        return match (true) {
            $parent instanceof Territory => ! $parent->children()->exists(),
            $parent instanceof Settlement => true,
            $parent instanceof Location => true,
            default => false,
        };
    }
}
