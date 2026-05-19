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
        $globalPermissions = Permission::where('recipient_type', 0)->where('recipient_id', 0)->get();
        if (count($globalPermissions) > 0) {
            foreach ($globalPermissions as $p) {
                $this->permissions[$p->subject_type][$p->subject_id][$p->permit->name] = $p->value;
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

        if (is_object($group->parent)) {
            $this->fetchGroupPermissions($group->parent, $depth + 1);
        }
        $this->setPermissions($group->permissions, $depth);
    }

    private function setPermissions($permissions, $depth = 1)
    {
        if (count($permissions) > 0) {
            foreach ($permissions as $p) {
                if (!$p->subject_id)
                    $this->permits[$p->permit->name] = $p->value;
                elseif (!isset($this->permissionDepth[$p->subject_type][$p->subject_id][$p->permit->name]) || $depth < $this->permissionDepth[$p->subject_type][$p->subject_id][$p->permit->name]) {
                    $this->permissions[$p->subject_type][$p->subject_id][$p->permit->name] = $p->value;
                    $this->permissionDepth[$p->subject_type][$p->subject_id][$p->permit->name] = $depth;
                }
            }
        }
    }
}
