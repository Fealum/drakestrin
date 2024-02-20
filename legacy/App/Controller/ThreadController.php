<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;
use Legacy\Library\Class\Cache;
use Legacy\Library\Class\Permission;
use Legacy\App\Model\ThreadModel;
use Legacy\App\Model\PostModel;

class ThreadController extends Controller
{

	protected $stdaction = 'view';

	function std()
	{
		$this->view(1, 1);
	}

	function view($id = 1, $page = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(1, $this->obj->id);
		$this->obj->update(array('views' => ($this->obj->views + 1)));
		if ($page == 'last') $page = ceil($this->obj->post__total / $this->config->pageentries);
		$this->set('page', $page);
		$this->set('obj', $this->obj);
		$this->set('viewed', $this->session->viewed);
		$viewedarray = $this->session->viewed;
		$viewedarray[1][$this->obj->id] = $this->obj->post__last_time;
		$this->session->viewed = $viewedarray;
	}

	function create()
	{
		if (Permission::getPermission(null, 'createthread') == 0) {
			$this->setnotice('thread_create_nopermission', 'error');
			return redirect('board');
		}
		$boards = new _list('board', 'board = 0', array('sort,a;name,a', array('sort' => 'sort', 'name' => 'name')));
		$this->set('boards', $boards);

		$thread = array('board' => $this->post('board'), 'name' => trim($this->post('name') ?? ''), 'post__first_time' => time(), 'post__total' => 1, 'post__last_time' => time(), 'views' => 0, 'important' => $this->post('important', 0));
		$post = array('user' => $this->user->id, 'character' => $this->post('character'), 'time' => $thread['post__first_time'], 'message' => trim($this->post('message') ?? ''), 'smilies' => $this->post('smilies', 0), 'signature' => $this->post('signature', 0), 'ip' => request()->ip());
		if ($thread['board'] && $thread['name'] && $post['message']) {
			$board = Cache::_('BoardModel', $thread['board']);
			if (Permission::getPermission($board, 'createthread') == 0) {
				$this->setnotice('thread_create_nopermission', 'error');
				$this->move('thread/create');
				exit;
			}
			$character = Cache::_('CharacterModel', $post['character']);
			if ($character->user->id != $this->user->id) {
				$this->move('thread/create');
				exit;
			}
			$newthread = new ThreadModel(NULL, $thread);
			$post['thread'] = $newthread->id;
			$newpost = new PostModel(NULL, $post);
			$newthread->update(array('post__first' => $newpost->id, 'post__last' => $newpost->id));
			$this->user->update(array('post__total' => ($this->user->post__total + 1)));
			$character->update(array('post__total' => ($character->post__total + 1)));
			$board->update(array('thread__total' => ($board->thread__total + 1), 'post__total' => ($board->post__total + 1), 'post__last' => $newpost->id));
			$this->move('thread/view/' . $newthread->id);
		}
		$this->set('thread', $thread);
		$this->set('post', $post);
	}

	function edit($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(1, $this->obj->id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'editthread') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'editthread') < 2) {
			$this->setnotice('thread_edit_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$thread = array('board' => $this->post('board'), 'name' => $this->post('name'), 'important' => $this->post('important', 0));
		if ($thread['name'] && $thread['board']) {
			$this->obj->update($thread);
			$this->move('thread/view/' . $this->obj->id);
		}
		$boards = new _list('board', 'board = 0', array('sort,a;name,a', array('sort' => 'sort', 'name' => 'name')));
		$this->set('boards', $boards);
		$this->set('thread', $thread);
	}

	function delete($id = 1)
	{
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
			$newlastpost = end($newlastpost);
			$board->update(array('thread__total' => ($board->thread__total - 1), 'post__total' => ($board->post__total - $postcount), 'post__last' => $newlastpost));
			$this->move('board');
		}
	}
}
