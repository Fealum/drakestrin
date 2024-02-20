<?php

namespace App\Services;

use App\Models\Permit;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    public function check($user, $object, $action)
    {
        $cacheKey = $this->generateCacheKey($user, $object, $action);

        // Try to get the result from the cache
        return Cache::remember($cacheKey, now()->addDays(1), function () use ($user, $object, $action) {
            // Perform the permission check and return the result
            // This is where you would implement the actual check logic
            return $this->performPermissionCheck($user, $object, $action);
        });
    }

    protected function generateCacheKey($subject, $object, $action)
    {
        // Generate a unique cache key based on the user, object, and action
        // Ensure that this key uniquely identifies the permission check
        return sprintf(
            'permissions:%s:%s:%s',
            $action,
            is_null($subject) ? 'global' : get_class($subject) . $subject->id,
            is_null($object) ? 'global' : get_class($object) . $object->id,
        );
    }

    protected function performPermissionCheck($subject, $object, $action)
    {
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

        if ($subject === NULL && $object === NULL) return $this->getPermit($action);

        if (is_object($object->{$object->permission})) {
            return self::$permissions[array_search($object->table, $tables)][$object->id][$permit] = self::getPermission($object->{$object->permission}, $permit);
        }

        return $this->getPermit($action);
        // Implement the actual permission checking logic here
    }

    protected function getPermit(string $action)
    {
        return Cache::remember('permits:' . $action, now()->addDays(1), function () use ($action) {
            return Permit::where('name', $action)->first();
        });
    }
}
