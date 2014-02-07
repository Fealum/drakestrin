<?php
class SearchController extends Controller {
	
	protected $stdaction = 'std';

	function std() {
	}

	function query() {
		$this->obj->user = new _list('user', "name LIKE '%".$this->post('query', '')."%'", array('name,a', array('name' => 'LOWER(name)')));
		$this->obj->board = new _list('board', "name LIKE '%".$this->post('query', '')."%'", array('name,a', array('name' => 'LOWER(name)')));
		$this->obj->thread = new _list('thread', "name LIKE '%".$this->post('query', '')."%'", array('name,a', array('name' => 'LOWER(name)')));
		$this->obj->post = new _list('post', "message LIKE '%".$this->post('query', '')."%'", array('time,d', array('time' => 'time')));
		$this->set('page', '1');
		$this->set('obj', $this->obj);
	}
}