<?php
class EncyclopediaController extends Controller {

	protected $stdaction = 'viewall';

	function std() {
		$this->viewall();
	}

	function viewall($order = 'sort,a;name,a') {
		$this->obj = new _list('encyclopedia', 'encyclopedia = 0', array($order, array('sort' => 'sort', 'name' => 'name')));
		$this->set('obj', $this->obj);
		$this->cacheid('viewall');
	}

	function view($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		$this->cacheid($this->obj->id);
	}

	function clearrecursivecache($obj) {
		$this->_view->clear('view', $obj->id);
		if($obj->encyclopedia != '0') $this->clearrecursivecache($obj->encyclopedia);
		else $this->_view->clear('viewall');
	}

	function create($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission($this->obj, 'createencyclopedia') == 0) {
			$this->setnotice('encyclopedia_create_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$encyclopedia = array('name' => trim($this->post('name')), 'title' => trim($this->post('title')), 'sort' => $this->post('sort'), 'encyclopedia' => $this->obj->id, 'text' => trim($this->post('text')), 'user' => $this->user->id, 'time' => time(), 'activated' => '1');
		if ($encyclopedia['name'] && $encyclopedia['title'] && $encyclopedia['text']) {
			$newencyclopedia = new EncyclopediaModel(NULL, $encyclopedia);
			$this->clearrecursivecache($this->obj);
			$this->move('encyclopedia/view/'.$newencyclopedia->id);
		}
	}
	
	function edit($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'editencyclopedia') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'editencyclopedia') < 2) {
			$this->notice[] = 'encyclopedia_edit_nopermission';
			$this->_view->change('view');
			exit;
		}
		$encyclopedia = array('encyclopedia' => $this->post('encyclopedia'), 'name' => trim($this->post('name')), 'title' => trim($this->post('title')), 'sort' => $this->post('sort'), 'text' => trim($this->post('text')));
		if ($encyclopedia['name'] && $encyclopedia['title'] && $encyclopedia['text']) {
			if($encyclopedia['name'] != $this->obj->name || $encyclopedia['encyclopedia'] != $this->obj->encyclopedia->id) {
				$this->clearrecursivecache($this->obj);
				$prevenc = $this->obj->encyclopedia->id;
			}
			else $this->_view->clear('view', $this->obj->id);
			$this->obj->update($encyclopedia);
			if($prevenc != $this->obj->encyclopedia->id) $this->clearrecursivecache($this->obj);
			$this->move('encyclopedia/view/'.$this->obj->id);
		}
		$pages = new _list('encyclopedia', 'encyclopedia = 0');
		$this->set('pages', $pages);
	}

	function delete($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
			if ((Permission::getPermission($this->obj, 'deleteencyclopedia') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'deleteencyclopedia') < 2) {
			$this->notice[] = 'encyclopedia_delete_nopermission';
			$this->_view->change('view');
			exit;
		}
		if ($this->post('delete') == 1) {
			$encyclopediaup = $this->obj->encyclopedia;
			$this->clearrecursivecache($this->obj);
			$this->obj->delete();
			header('Location: '.$this->config->url.'/encyclopedia/view/'.$encyclopediaup->id);
		}
	}
}