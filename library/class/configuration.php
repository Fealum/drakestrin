<?php
class Configuration {
	public static $instance = NULL;
	private static $user;
	private static $configurations = array();
	private static $configurationdepth = array();
	private static $settings = array();

	private function __construct() {
	}

	private static function setConfigurations_set($configurations, $depth = 1) {
		if (is_array($configurations)) {
			foreach ($configurations as $p) {
				if (!$p->subject) 
					self::$settings[$p->setting->name] = $p->value;
				elseif (!isset(self::$configurationdepth[$p->table__subject][$p->subject->id][$p->setting->name]) || $depth < self::$configurationdepth[$p->table__subject][$p->subject->id][$p->setting->name]) {
					self::$configurations[$p->table__subject][$p->subject->id][$p->setting->name] = $p->value;
					self::$configurationdepth[$p->table__subject][$p->subject->id][$p->setting->name] = $depth;
				}
			}
		}
	}

	private static function setConfigurations_group($group, $depth = 2) {
		if (is_object($group->group__parent))
			self::setConfigurations_group($group->group__parent, $depth + 1);
		self::setConfigurations_set($group->configuration___recipient, $depth);
	}

	public static function setConfigurations(&$user = NULL) {
		$settings = new _list('setting');
		foreach ($settings->data as $p) 
			self::$settings[$p->name] = $p->standard;
		$globalconfigurations = new _list('configuration', 'table__recipient = 0 AND recipient = 0');
		if ($globalconfigurations->data) {
			foreach ($globalconfigurations->data as $p) 
				self::$configurations[$p->table__subject][$p->subject->id][$p->setting->name] = $p->value;
		}
		if ($user) {
			self::$user = &$user;
			if (is_array(self::$user->group)) foreach (self::$user->group as $group) self::setConfigurations_group($group);
			self::setConfigurations_set(self::$user->configuration___recipient);
		}
	}

	public static function getInstance() {
		if (!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

	public static function getConfiguration($object, $setting) {
		global $tables;
		if ($object == NULL) return self::$settings[$setting];
		if (isset(self::$configurations[array_search($object->table, $tables)][$object->id][$setting])) 
			return self::$configurations[array_search($object->table, $tables)][$object->id][$setting];
		elseif (is_object($object->{$object->configuration})) 
			return self::$configurations[array_search($object->table, $tables)][$object->id][$setting] = self::getConfiguration($object->{$object->configuration}, $setting);
		else
			return self::$settings[$setting];
	}
	
	public static function serializeConfiguration($object) {
		$settings = new _list('setting');
		$configurationarray = array();
		foreach ($settings->data as $p) 
			$configurationarray[$p->name] = self::getConfiguration($object, $p->name);
		return serialize($configurationarray); 
	}
}
?>