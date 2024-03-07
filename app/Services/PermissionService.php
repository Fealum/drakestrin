<?php

namespace App\Services;

use App\Models\Permit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    private ?array $permissions = null;
    private ?array $permits = null;

    public function __construct()
    {
    }

    private function instanciate()
    {
        $userId = auth()->id();
        $permissionCacheKey = 'user_permissions:' . ($userId ?? 'global');
        $permitCacheKey = 'user_permits:' . ($userId ?? 'global');

        $this->permissions = Cache::get($permissionCacheKey, []);
        $this->permits = Cache::get($permitCacheKey, []);
    }

    public function check($action, $object = NULL)
    {
        // Legacy conversion
        $action = strtolower($action);
        // End Legacy conversion

        if (!$this->permissions) {
            $this->instanciate();
        }
        $tables = array(
            0 => 'user',
            1 => 'thread',
            2 => 'company',
            3 => 'board',
            4 => 'group',
            5 => 'encyclopedia',
            6 => 'character',
            8 => 'company_worker'
        );

        if ($object == NULL) return $this->permits[$action];

        $tablesKey = array_search($object->table, $tables);

        if (isset($this->permissions[$tablesKey][$object->id][$action])) {
            return $this->permissions[$tablesKey][$object->id][$action];
        } elseif (is_object($object->{$object->permission})) {
            return $this->permissions[$tablesKey][$object->id][$action] = $this->check($action, $object->{$object->permission});
        } else {
            return $this->permits[$action];
        }
    }
}
