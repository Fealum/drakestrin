<?php
class PostController extends Controller {

	protected $stdaction = 'view';

	function std() {
		$this->view(1, 1);
	}

	function view($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}

	function create($id = 1) {
		$this->obj = Cache::_('ThreadModel', $id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission($this->obj, 'createpost') == 0) {
			$this->setnotice('post_create_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$post = array('thread' => $this->obj->id, 'user' => $this->user->id, 'time' => time(), 'message' => $this->post('message'), 'smilies' => $this->post('smilies', 0), 'signature' => $this->post('signature', 0), 'ip' => $_SERVER['REMOTE_ADDR']);
		if ($post['message']) {
			$newpost = new PostModel(NULL, $post);
			$this->obj->update(array('post__total' => ($this->obj->post__total + 1), 'post__last' => $newpost->id, 'post__last_time' => $newpost->time));
			$this->user->update(array('post__total' => ($this->user->post__total + 1)));
			$this->obj->board->update(array('post__total' => ($this->obj->board->post__total + 1), 'post__last' => $newpost->id));
			$this->move('thread/view/'.$this->obj->id.'/last#post'.$newpost->id);
		}
		$this->set('post', $post);
	}

	function edit($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'editpost') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'editpost') < 2) {
			$this->setnotice('post_edit_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$post = array('message' => $this->post('message'));
		if ($post['message']) {
			$this->obj->update($post);
			$this->move('thread/view/'.$this->obj->thread->id);
		}
		$this->set('post', $post);
	}

	function delete($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'deletepost') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'deletepost') < 2) {
			$this->setnotice('post_delete_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		if ($this->post('delete') == 1) {
			$thread = $this->obj->thread;
			$user = $this->obj->user;
			$this->obj->delete();
			$newlastthread = new ThreadModel;
			$newlastthread->id_from_order(array('id,d', array('id' => 'id')));
			$newlastpost = $newlastthread->post;
			$newlastpost = &end($newlastpost);
			$thread->update(array('post__total' => ($thread->post__total - 1)));
			$user->update(array('post__total' => ($user->post__total - 1)));
			$thread->board->update(array('post__total' => ($thread->board->post__total - 1), 'post__last' => $newlastpost));
			$this->move('thread/view/'.$thread->id);
		}
	}
}