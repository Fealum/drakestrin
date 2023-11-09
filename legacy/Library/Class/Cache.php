<?php

namespace Legacy\Library\Class;

define('STDSOURCE', 'mysql1');

class Cache
{
	private static $cache = array();

	private function __construct()
	{
	}

	public static function _($cache, $id)
	{
		$id = (int)$id;
		$cacheClass = '\Legacy\App\Model\\' . $cache;
		if (!isset(self::$cache[$cache][$id])) self::$cache[$cache][$id] = new $cacheClass($id);
		return self::$cache[$cache][$id];
	}
}
