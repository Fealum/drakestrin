<?php
class TerritoryController extends Controller {

	protected $stdaction = 'view';

	function std() {
		// $this->obj = new _list('territory', 'territory = 1', array('name,a', array('name' => 'name')));
		// $this->set('obj', $this->obj);
		$this->view();
	}
	
	function view($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}
	
	function ajax__getterritories($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}
	
	function ajax__getterritorydata($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}

	function ajax__getterritoryland() {
	}
}
