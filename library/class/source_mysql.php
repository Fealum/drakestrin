<?php
class Source_MySQL {

	protected $_dbHandle, $p;

	public function __construct($data) {
		$this->_dbHandle = new mysqli($data['host'], $data['user'], $data['password'], $data['database']);
		$this->_dbHandle->set_charset("utf8mb4");
		$this->p = $data['prefix'];
		if ($this->_dbHandle->connect_error) die('Connect Error ('.$this->_dbHandle->connect_errno.')'.$this->_dbHandle->connect_error);
	}

	public function __destruct() {
		$this->_dbHandle->close();
	}

	function query($query_string) {
		return $this->_dbHandle->query($query_string);
	}
	
	function escape($string) {
		return $this->_dbHandle->real_escape_string($string);
	}
	
	function insert_id() {
		return $this->_dbHandle->insert_id;
	}
	
	protected function createorder($order) {
		$allowed = $order[1];
		$order = $order[0];
		$order = explode(';', $order);
		foreach ($order as $key => $value) {
			$value = explode(',', $value);
			if (!array_key_exists($value[0], $allowed)) unset($order[$key]);
			else {
				$value[0] = $allowed[$value[0]];
				$value[1] = ($value[1] == 'a') ? ' ASC' : ' DESC'; 
				$order[$key] = $value[0].$value[1];
			}
		}
		$order = 'ORDER BY '.join(', ', $order);
		return $order;
	}
	
	public function select($table, $where = NULL, $fields = array('*'), $order = NULL, $limit = NULL, $group = NULL, $geomfields = NULL) {
		if ($limit == NULL) $limit = array('', '');
		$limit[0] = (int)$limit[0];
		if($limit[0] > 0) 
			$limit[0] = ($limit[1] > 0) ? 'LIMIT '.$limit[1].','.$limit[0] : 'LIMIT '.$limit[0];
		else $limit[0] = '';

		$grouporder = $order;
		$order = $order ? $this->createorder($order) : 'ORDER BY id';
		$group = $group ? 'GROUP BY `'.$group.'`'.($grouporder ? ', `'.join('`, `', $grouporder[1]).'`' : '') : '';
		if (is_int($where)) $where = 'WHERE id = '.$where;
		elseif (!is_null($where)) $where = 'WHERE '.$where;
		if ($geomfields != NULL) {
			foreach ($geomfields as $key => $val)
				$geomfields[$key] = 'ST_AsBinary(`'.$val.'`) AS '.$val;	
			$geomfields = ', ' . join('`), ST_AsBinary(`', $geomfields);
		}
		$result = $this->query('
			SELECT `'.join('`, `', $fields).'`'.$geomfields.'
			FROM `'.$this->p.$table.'`
			'.$where.'
			'.$group.'
			'.$order.'
			'.$limit[0].'
		');

		if ($result == true) {
			$data = array();
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) $data[] = $row;
			$result->free();
			return $data;
		}
		else return false;
	}
}