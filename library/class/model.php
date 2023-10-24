<?php
/**
 * Basic abstract class for data from the database. Referenced by specific data classes.  
 * @author Fabian
 */
abstract class Model {
	protected $table, $id, $data, $source = STDSOURCE;

	/**
	 * Constructor. Constructs data object either from an ID or a query, in which case it 
	 * inserts the query into the database and receives its ID as the object ID.
	 * @param int $id
	 * @param array $query Each key is the data name and each value is the data value.
	 */
	public function __construct($id = NULL, array $query = NULL) {
		$this->table = substr(strtolower(get_class($this)), 0, -5);
		$this->data = $this->datatypes;
		foreach ($this->data as &$value) $value = NULL;
		if (is_null($id) && !is_null($query)) {
			// declare and check
			foreach ($query AS $name => $value) {
				$this->__set($name, $value);
				$querynames[] = $name;
				$queryvalues[] = "'".Source::getInstance($this->source)->escape($this->data[$name])."'";
			}
			// insert into database
			Source::getInstance($this->source)->query("
				INSERT INTO dra_".$this->table." (`".join('`, `', $querynames)."`)
				VALUES (".join(', ', $queryvalues).") 
			");
			// declare id
			$this->__set('id', Source::getInstance($this->source)->insert_id());
		}
		elseif (!is_null($id)) {
			if (is_numeric($id)) $this->__set('id', $id);
			else $this->id_from_order(NULL, $id);
			// check if permission allows object to be shown.
			if (isset($this->permission)) if (Permission::getPermission($this, 'show') == 0) unset($this->datatypes);
		}
	}

	/**
	 * Magic set function. Sets the new value if it is either an int or a string. 
	 * @param string $name
	 * @param string $value
	 */
	public function __set($name, $value) {
		if ($name == 'id') $this->id = (int)$value;
		elseif (in_array($this->datatypes[$name], array('int', 'string'))) settype($value, $this->datatypes[$name]);
		if ($name != 'id' && isset($this->datatypes[$name])) {
			if ($this->datatypes[$name] == 'geom') $this->data[$name] = ($value != NULL) ? geo::load($value,'wkb') : NULL;
			else $this->data[$name] = $value;
		}
	}

	public function __get($name) {
		global $tables;
		if ($name == 'id') return $this->id;
		elseif ($name == 'table') return $this->table;
		elseif ($name == 'permission' && isset($this->permission)) return $this->permission;
		elseif (!isset($this->datatypes[$name])) return FALSE;
		elseif (!isset($this->data[$name]) && !in_array($this->datatypes[$name], array('relation', 'children', 'mchildren', 'custom'))) $this->select();
		if ($this->datatypes[$name] == 'hidden') return $this->data[$name];
		elseif ($this->datatypes[$name] == 'relation' && !isset($this->data[$name])) isset($this->dataorder[$name]) ? $this->initrelations($name, $this->dataorder[$name]) : $this->initrelations($name);
		elseif ($this->datatypes[$name] == 'children' && !isset($this->data[$name])) isset($this->dataorder[$name]) ? $this->initchildren($name, $this->dataorder[$name]) : $this->initchildren($name);
		elseif ($this->datatypes[$name] == 'mchildren' && !isset($this->data[$name])) $this->initmchildren($name);
		elseif ($this->datatypes[$name] == 'custom' && !isset($this->data[$name])) $this->{'init_'.$name}();
		elseif ($this->datatypes[$name] == 'parent' && !is_object($this->data[$name])) $this->init($name, $this->data[$name]);
		elseif ($this->datatypes[$name] == 'mparent' && !is_object($this->data[$name])) $this->init($tables[$this->__get('table__'.$name)], $this->data[$name], $name);
		elseif (!isset($this->datatypes[$name])) echo 'Unbekannter Datentyp: '.$this->datatypes[$name];
		if (isset($this->data[$name])) return $this->data[$name];
	}

	/**
	 * Magic toString function. Returns the ID of the object, if it is used as a string.
	 * @return string
	 */
	public function __toString() {
		return (string)$this->id;
	}
	
	/**
	 * Strips suffixes marked with __ from a string. 
	 * @param string $name
	 * @return string
	 */
	protected function stripsuffix($name) {
		return (string)preg_replace('/__(.+)/', '', $name);
	}
	
	/**
	 * Strips prefixes marked with __ from a string. 
	 * @param string $name
	 * @return string
	 */
	protected function stripprefix($name) {
		return (string)preg_replace('/(.+)___/', '', $name);
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

	protected function init($name, $id, $dataname = NULL) {
		if ($dataname == NULL) $dataname = $name;
		if (is_array($id)) {
			foreach ($id as $value) 
				if (Permission::getPermission(Cache::_(getModelNameFromObject($this->stripsuffix($name)), $value), 'show') > 0 && $value != 0) $this->data[$dataname][] = Cache::_(getModelNameFromObject($this->stripsuffix($name)), $value);
			if (empty($this->data[$dataname])) $this->data[$dataname] == FALSE;
		}
		elseif ($id != 0) $this->data[$dataname] = Cache::_(getModelNameFromObject($this->stripsuffix($name)), $id);
		else $this->data[$name] = FALSE;
		
	}

	protected function initchildren($name, $order = NULL) {
		if ($this->stripprefix($name) != $name) {
			$underscores = '__'.$this->stripprefix($name);
		}
		else $underscores = '';
		$query = Source::getInstance($this->source)->select($this->stripsuffix($name), "`".$this->table.$underscores."` = ".$this->id, array('id'), $order);
		if ($query) foreach ($query as $result) $array[] = (int)$result['id'];
		if(isset($array)) $this->init($name, $array);
		else $this->init($name, FALSE);
	}
	
	protected function initmchildren($name) {
		global $tables;
		$namepcs = explode('___', $name);
		$query = Source::getInstance($this->source)->query("
			SELECT id 
			FROM `dra_".$this->stripsuffix($namepcs[0])."` 
			WHERE `table__".$namepcs[1]."` = ".array_search($this->table, $tables)." AND 
				`".$namepcs[1]."` = ".$this->id." 
			ORDER BY id
		");
		while ($result = $query->fetch_assoc()) $array[] = (int)$result['id'];
		if (empty($array)) $array = false;
		$this->init($name, $array);
	}

	protected function initrelations($name, $order = NULL) {
		$table = array($name, $this->table);
		sort($table);
		$table = join('2', $table);
		$order = $order ? $this->createorder($order) : 'ORDER BY id';
		$query = Source::getInstance($this->source)->query("
			SELECT `".$table."`.`".$this->stripsuffix($name)."` AS `relationid`
			FROM `dra_".$table."` AS `".$table."`
			LEFT JOIN `dra_".$this->stripsuffix($name)."` AS `".$this->stripsuffix($name)."`
			ON `".$table."`.`".$this->stripsuffix($name)."` = `".$this->stripsuffix($name)."`.`id`
			WHERE `".$table."`.`".$this->table."` = ".$this->id." 
			".$order."
		");
		$array = array();
		while ($result = $query->fetch_assoc()) $array[(int)$result['relationid']] = (int)$result['relationid'];
		$this->init($name, $array);
	}
	
	public function createrelation($name, $obj) {
		$table = array($name, $this->table);
		sort($table);
		$table = join('2', $table);
		$query = Source::getInstance($this->source)->query("
			INSERT INTO `dra_".$table."` (`".$name."`, `".$this->table."`)
			VALUES (".$obj->id.", ".$this->id.")
		");
		if(isset($this->data[$name])) unset($this->data[$name]);
	}
	
	/**
	 * Gets the ID for a unique data value (or otherwise the ID for the first data with this value).
	 * @param string $key Name of the data column.
	 * @param string $val Data value.
	 */
	public function id_from_unique($key, $val) {
		$query = Source::getInstance($this->source)->select($this->table, "`".Source::getInstance($this->source)->escape($key)."` = '".Source::getInstance($this->source)->escape($val)."'", array('id'));
		if (count($query) > 0) {
			$this->__set('id', $query[0]['id']);
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * Gets the ID for the first data in a given order.
	 * @param array $order Order of the data.
	 */
	public function id_from_order($order, $where = NULL) {
		$query = Source::getInstance($this->source)->select($this->table, $where, array('id'), $order);
		if (count($query) > 0) {
			$this->__set('id', $query[0]['id']);
			return TRUE;
		}
		else return FALSE;
	}

	/**
	 * Selects all values from the database and writes them into the corresponding variables
	 * of the data object.
	 */
	protected function select() {
		// check if data are set
		if (!isset($this->data)) return;
		// get column names
		$queryarray = $this->data;
		$geomarray = array();
		foreach ($queryarray as $key => $value) {
			if (in_array($this->datatypes[$key], array('relation', 'children', 'mchildren', 'custom'))) unset($queryarray[$key]);
			if ($this->datatypes[$key] == 'geom') {
				$geomarray[$key] = $value;
				unset($queryarray[$key]);
			}
		}
		// get data from database
		$query = Source::getInstance($this->source)->select($this->table, $this->id, array_keys($queryarray), NULL, NULL, NULL, (empty($geomarray)) ? NULL : array_keys($geomarray));
		if (isset($query[0])) {
			$query = $query[0];
			foreach ($query AS $name => $value) $this->__set($name, $value);
		}
	}

	/**
	 * Updates all given data of the referenced object both in the object and the database.
	 * @param array $objects Each key is the name of the data; each value is its new value. 
	 */
	public function update(array $objects) {
		if(is_array($objects)) {
			$query = array();
			foreach ($objects as $name => $value) {
				$this->__set($name, $value);
				if (!is_null($value)) $query[] = "`".$name."` = '".Source::getInstance($this->source)->escape($this->__get($name))."'";
				else $query[] = "`".$name."` = NULL";
			}

			// update database
			Source::getInstance($this->source)->query("
				UPDATE dra_".$this->table." 
				SET ".join(', ', $query)." 
				WHERE id = ".$this->id."
			");
		}
	}

	/**
	 * Deletes the data of this data object from the database. 
	 */
	public function delete() {

		// delete from database
		Source::getInstance($this->source)->query("
			DELETE FROM dra_".$this->table." 
			WHERE id = ".$this->id." 
		");
	}
}
?>