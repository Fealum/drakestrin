<?php
class Source {
	public static $instance = array();
	private static $sources = array(
		'mysql1' => array(
			'type' => 'MySQL',
			'host' => '',
			'user' => '',
			'password' => '',
			'database' => '',
			'prefix' => ''
		)
	);

	private function __construct() {
	}

	// singleton pattern
	static function getInstance($instance) {
		if(!isset(self::$instance[$instance])) {
			$classname = 'Source_'.self::$sources[$instance]['type'];
			self::$instance[$instance] = new $classname(self::$sources[$instance]);
		}
		return self::$instance[$instance];
	}
}
