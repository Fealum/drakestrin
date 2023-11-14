<?php

namespace Legacy\Library\Class;

use Legacy\Library\Class\Source;
use Legacy\Library\Class\Cache;
use Legacy\Library\Class\Permission;

class _list
{
	protected $table, $data, $source = 'mysql1';

	public function __construct($table, $where = NULL, $order = NULL, $limit = array('', ''), $permission = false, $group = NULL, $idcol = 'id')
	{
		$this->table = $table;
		$query = Source::getInstance($this->source)->select($this->table, $where, array($idcol), $order, $limit, $group);
		if (!empty($query)) {
			foreach ($query as $result) {
				$obj = Cache::_(getModelNameFromObject($this->table), $result[$idcol]);
				if (!$permission || $permission && Permission::getPermission($obj, 'show') > 0) {
					$this->data[] = $obj;
				}
			}
		} else $this->data = NULL;
	}

	public function __get($name)
	{
		if ($name == 'data') return $this->data;
	}
}
