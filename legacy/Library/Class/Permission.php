<?php

namespace Legacy\Library\Class;

use App\Models\Permission as ModelsPermission;
use App\Models\Permit;
use Illuminate\Support\Facades\Cache;

class Permission
{
	public static $instance = NULL;
	private static $permissions = array();
	private static $permits = array();

	private function __construct()
	{
	}

	public static function setPermissions(&$user = NULL)
	{
		$userId = $user?->id;
		$permissionCacheKey = 'user_permissions:' . ($userId ?? 'global');
		$permitCacheKey = 'user_permits:' . ($userId ?? 'global');

		self::$permissions = Cache::get($permissionCacheKey, []);
		self::$permits = Cache::get($permitCacheKey, []);
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

	public static function getPermission($object, $permit)
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

		if ($object == NULL) return self::$permits[$permit];

		$tablesKey = array_search($object->table, $tables);

		if (isset(self::$permissions[$tablesKey][$object->id][$permit])) {
			return self::$permissions[$tablesKey][$object->id][$permit];
		} elseif (is_object($object->{$object->permission})) {
			return self::$permissions[$tablesKey][$object->id][$permit] = self::getPermission($object->{$object->permission}, $permit);
		} else {
			return self::$permits[$permit];
		}
	}
}
