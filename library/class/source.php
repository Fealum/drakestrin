<?php
class Source {
	private static $instance = [];

	private function __construct(String $instance) {

	}

	// singleton pattern
	public static function getInstance(String $instance) {
		if(!isset(self::$instance[$instance])) {
			$sources['mysql1'] = array(
				'type' => 'MySQL',
				'host' => $_ENV['DB_HOST'],
				'user' => $_ENV['DB_USER'],
				'password' => $_ENV['DB_PW'],
				'database' => $_ENV['DB_NAME'],
				'prefix' => 'dra_'
			);
			$classname = 'Source_'.$sources[$instance]['type'];
			self::$instance[$instance] = new $classname($sources[$instance]);
		}
		return self::$instance[$instance];
	}
}
