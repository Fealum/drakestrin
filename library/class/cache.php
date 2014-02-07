<?php
class Cache {
	private static $cache = array();

	private function __construct() {
	}

	public static function _($cache, $id) {
		$id = (int)$id;
		if(!isset(self::$cache[$cache][$id])) self::$cache[$cache][$id] = new $cache($id);
		return self::$cache[$cache][$id];
	}
}
?>