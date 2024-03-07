<?php

namespace App\Http\Middleware;

use App\Models\Permission;
use App\Models\Permit;
use App\Models\User;
use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FetchPermissions
{
    private User $user;
    private array $permissions = [];
    private array $permissionDepth = [];
    private array $permits = [];

    public function handle(Request $request, Closure $next)
    {

        $userId = auth()->id();

        $permissionCacheKey = 'user_permissions:' . ($userId ?? 'global');
        $permitCacheKey = 'user_permits:' . ($userId ?? 'global');

        $this->calculateUserPermissions($userId);

        $permissions = Cache::remember($permissionCacheKey, now()->addDay(), function () {
            return $this->permissions;
        });

        $request->attributes->set('userPermissions', $permissions);

        $permits = Cache::remember($permitCacheKey, now()->addDay(), function () {
            return $this->permits;
        });

        $request->attributes->set('userPermits', $permits);

        return $next($request);
    }

    protected function calculateUserPermissions($userId)
    {
        $permits = Permit::all();
        foreach ($permits as $permit) {
            $this->permits[$permit->name] = $permit->standard;
        }
        $globalPermissions = Permission::where('table__recipient', 0)->where('recipient', 0)->get();
        if (count($globalPermissions) > 0) {
            foreach ($globalPermissions as $p) {
                $this->permissions[$p->table__subject][$p->subject][$p->permit_legacy->name] = $p->value;
            }
        }
        if ($userId) {
            $this->user = User::find($userId);
            if (count($this->user->groups) > 0) {
                foreach ($this->user->groups as $group) {
                    $this->fetchGroupPermissions($group);
                }
            }
            $this->setPermissions($this->user->permissions);
        }

        return $this->permissions;
    }

    private function fetchGroupPermissions($group, $depth = 2)
    {

        if (is_object($group->group)) {
            $this->fetchGroupPermissions($group->group, $depth + 1);
        }
        $this->setPermissions($group->permissions, $depth);
    }

    private function setPermissions($permissions, $depth = 1)
    {
        if (count($permissions) > 0) {
            foreach ($permissions as $p) {
                if (!$p->subject)
                    $this->permits[$p->permit_legacy->name] = $p->value;
                elseif (!isset($this->permissionDepth[$p->table__subject][$p->subject][$p->permit_legacy->name]) || $depth < $this->permissionDepth[$p->table__subject][$p->subject][$p->permit_legacy->name]) {
                    $this->permissions[$p->table__subject][$p->subject][$p->permit_legacy->name] = $p->value;
                    $this->permissionDepth[$p->table__subject][$p->subject][$p->permit_legacy->name] = $depth;
                }
            }
        }
    }
}
