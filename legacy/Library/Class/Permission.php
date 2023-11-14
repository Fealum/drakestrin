<?php

namespace Legacy\Library\Class;

class Permission
{
	public static $instance = NULL;
	private static $user;
	private static $permissions = array();
	private static $permissiondepth = array();
	private static $permits = array();

	private function __construct()
	{
	}

	private static function setPermissions_set($permissions, $depth = 1)
	{
		if (is_array($permissions)) {
			foreach ($permissions as $p) {
				if (!$p->subject)
					self::$permits[$p->permit->name] = $p->value;
				elseif (!isset(self::$permissiondepth[$p->table__subject][$p->subject->id][$p->permit->name]) || $depth < self::$permissiondepth[$p->table__subject][$p->subject->id][$p->permit->name]) {
					self::$permissions[$p->table__subject][$p->subject->id][$p->permit->name] = $p->value;
					self::$permissiondepth[$p->table__subject][$p->subject->id][$p->permit->name] = $depth;
				}
			}
		}
	}

	private static function setPermissions_group($group, $depth = 2)
	{
		if (is_object($group->group__parent))
			self::setPermissions_group($group->group__parent, $depth + 1);
		self::setPermissions_set($group->permission___recipient, $depth);
	}

	public static function setPermissions(&$user = NULL)
	{
		$permits = new _list('permit');
		foreach ($permits->data as $p)
			self::$permits[$p->name] = $p->standard;
		$globalpermissions = new _list('permission', 'table__recipient = 0 AND recipient = 0');
		if ($globalpermissions->data) {
			foreach ($globalpermissions->data as $p)
				self::$permissions[$p->table__subject][$p->subject->id][$p->permit->name] = $p->value;
		}
		if ($user) {
			self::$user = &$user;
			if (is_array(self::$user->group)) foreach (self::$user->group as $group) self::setPermissions_group($group);
			self::setPermissions_set(self::$user->permission___recipient);
		}
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
		if (isset(self::$permissions[array_search($object->table, $tables)][$object->id][$permit]))
			return self::$permissions[array_search($object->table, $tables)][$object->id][$permit];
		elseif (is_object($object->{$object->permission}))
			return self::$permissions[array_search($object->table, $tables)][$object->id][$permit] = self::getPermission($object->{$object->permission}, $permit);
		else
			return self::$permits[$permit];
	}

	public static function serializePermission($object)
	{
		$permits = new _list('permit');
		$permissionarray = array();
		foreach ($permits->data as $p)
			$permissionarray[$p->name] = self::getPermission($object, $p->name);
		return serialize($permissionarray);
	}
}
