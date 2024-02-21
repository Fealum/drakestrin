<?php

namespace Legacy\Library\Class;

class Source
{
	private static $instance = [];

	private function __construct(String $instance)
	{
	}

	// singleton pattern
	public static function getInstance(String $instance)
	{
		if (!isset(self::$instance[$instance])) {
			$sources['mysql1'] = array(
				'type' => 'MySQL',
				'host' => config('database.connections.mysql.host'),
				'user' => config('database.connections.mysql.username'),
				'password' => config('database.connections.mysql.password'),
				'database' => config('database.connections.mysql.database'),
				'prefix' => 'dra_'
			);
			$classname = '\Legacy\Library\Class\Source_' . $sources[$instance]['type'];
			self::$instance[$instance] = new $classname($sources[$instance]);
		}
		return self::$instance[$instance];
	}
}
