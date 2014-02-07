<?php
class ThreadController extends Controller {

	protected $stdaction = 'view';

	function std() {
		$this->view(1, 1);
	}

	function view($id = 1, $page = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(1, $this->obj->id);
		$this->obj->update(array('views' => ($this->obj->views + 1)));
		if ($page == 'last') $page = ceil($this->obj->post__total / $this->config->pageentries);
		$this->set('page', $page);
		$this->set('obj', $this->obj);
		$viewedarray = $this->session->viewed;
		$viewedarray[1][$this->obj->id] = $this->obj->post__last_time;
		$this->session->viewed = $viewedarray;
	}

	function create($id = 1) {
		$this->obj = Cache::_('BoardModel', $id);
		$this->onlinelocation(3, $this->obj->id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission($this->obj, 'createthread') == 0) {
			$this->setnotice('thread_create_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$thread = array('board' => $this->obj->id, 'name' => $this->post('name'), 'time' => time(), 'post__total' => 1, 'post__last_time' => time(), 'views' => 0, 'important' => $this->post('important', 0));
		$post = array('user' => $this->user->id, 'time' => $thread['time'], 'message' => $this->post('message'), 'smilies' => $this->post('smilies', 0), 'signature' => $this->post('signature', 0), 'ip' => $_SERVER['REMOTE_ADDR']);
		if ($thread['name'] && $post['message']) {
			$newthread = new ThreadModel(NULL, $thread);
			$post['thread'] = $newthread->id;
			$newpost = new PostModel(NULL, $post);
			$newthread->update(array('post__last' => $newpost->id));
			$this->user->update(array('post__total' => ($this->user->post__total + 1)));
			$this->obj->update(array('thread__total' => ($this->obj->thread__total + 1), 'post__total' => ($this->obj->post__total + 1), 'post__last' => $newpost->id));
			$this->move('thread/view/'.$newthread->id);
		}
		$this->set('thread', $thread);
		$this->set('post', $post);
	}

	function edit($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(1, $this->obj->id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'editthread') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'editthread') < 2) {
			$this->setnotice('thread_edit_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$thread = array('name' => $this->post('name'), 'important' => $this->post('important', 0));
		if ($thread['name']) {
			$this->obj->update($thread);
			$this->move('thread/view/'.$this->obj->id);
		}
		$this->set('thread', $thread);
	}

	function delete($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(1, $this->obj->id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'deletethread') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'deletethread') < 2) {
			$this->setnotice('thread_delete_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		if ($this->post('delete') == 1) {
			$board = $this->obj->board;
			$postcount = count($this->obj->post);
			foreach ($this->obj->post as $i) {
				$i->user->update(array('post__total' => ($i->user->post__total - 1)));
				$i->delete();
			}
			$this->obj->delete();
			$newlastthread = new ThreadModel;
			$newlastthread->id_from_order(array('id,d', array('id' => 'id')));
			$newlastpost = $newlastthread->post;
			$newlastpost = &end($newlastpost);
			$board->update(array('thread__total' => ($board->thread__total - 1), 'post__total' => ($board->post__total - $postcount), 'post__last' => $newlastpost));
			$this->move('board/view/'.$board->id);
		}
	}
}