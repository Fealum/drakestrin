<?php
class _list {
	protected $table, $data, $source = STDSOURCE;

	public function __construct($table, $where = NULL, $order = NULL, $limit__count = '', $limit__offset = '') {
		$this->table = $table;
		$query = Source::getInstance($this->source)->select($this->table, $where, array('id'), $order, $limit__count, $limit__offset);
		if (is_array($query)) foreach ($query as $result) $this->data[] = Cache::_(getModelNameFromObject($this->table), $result['id']);
		else $this->data = NULL;
	}

	public function __get($name) {
		if ($name == 'data') return $this->data;
	}
}