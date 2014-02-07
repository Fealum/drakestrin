<?php
class BoardController extends Controller {
	
	protected $stdaction = 'viewall';

	function std($order = 'sort,a;name,a') {
		$this->viewall($order);
	}

	function viewall($order = 'sort,a;name,a') {
		$this->obj = new _list('board', 'board = 0', array($order, array('sort' => 'sort', 'name' => 'name')));
		if (!is_null($this->user)) $this->set('viewed', $this->getviewed($this->obj->data));
		$this->set('obj', $this->obj);
	}
	
	function getviewed($array) {
		$viewed = array();
		if ($array) foreach ($array as $cur) {
			$threads = new _list('thread', 'board = '.$cur->id.' AND post__last_time >= '.$this->user->lastvisit);
			if ($threads->data) foreach ($threads->data as $i) {
				if ((isset($this->session->viewed[1][$i->id]) && $this->session->viewed[1][$i->id] < $i->post__last_time) || !isset($this->session->viewed[1][$i->id])) {
					$viewed[$cur->id] = TRUE;
					break;
				}
			}
			if ($cur->board__1) $viewed = $viewed + $this->getviewed($cur->board__1);
		}
		return $viewed;
	}
	
	function changeshow($id = 1) {
		if (is_null($this->user)) $this->move('board');
		$board = Cache::_($this->_model, $id);
		$configurations = new _list('configuration', 'table__recipient = 0 AND recipient = '.$this->user->id.' AND table__subject = 3 AND subject = '.$board->id.' AND setting = 4');
		if (Configuration::getConfiguration($board, 'show') == '0') $change = '1';
		else $change = '0';
		if (isset($configurations->data[0])) {
			$configurations->data[0]->update(array('value' => $change));
		}
		else {
			new ConfigurationModel(NULL, array('table__recipient' => '0', 'recipient' => $this->user->id, 'table__subject' => '3', 'subject' => $board->id, 'setting' => 4, 'value' => $change));
		}
		$this->move('board');
	}

	function view($id = 1, $page = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(3, $this->obj->id);
		$this->set('page', $page);
		if (!is_null($this->user)) $this->set('viewedboards', $this->getviewed($this->obj->board__1));
		$this->set('viewed', $this->session->viewed);
		$this->set('obj', $this->obj);
	}
	
	function permissions($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(3, $this->obj->id);
		$this->set('obj', $this->obj);
	}
}