<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\Cache;
use Legacy\Library\Class\Permission;
use Legacy\App\Model\CharacterModel;
use Legacy\App\Model\PostModel;
use Legacy\App\Model\ThreadModel;

class PostController extends Controller
{

	protected $stdaction = 'view';

	function std()
	{
		$this->view(1, 1);
	}

	function view($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}

	function create($id = 1)
	{
		$this->obj = Cache::_('ThreadModel', $id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission($this->obj, 'createpost') == 0) {
			$this->setnotice('post_create_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$post = array('thread' => $this->obj->id, 'user' => $this->user->id, 'character' => $this->post('character'), 'time' => time(), 'message' => trim($this->post('message')), 'smilies' => $this->post('smilies', 0), 'signature' => $this->post('signature', 0), 'ip' => request()->ip());
		if ($post['message']) {
			if ($post['character'] == 'new') {
				if (Permission::getPermission($this->obj, 'createcharacter') < 1) {
					$this->setnotice('user_createcharacter_nopermission', 'error');
					$this->move('thread/view/' . $this->obj->id);
				} else {
					$character = array('name' => $this->post('newcharname'), 'regdate' => time(), 'user' => $this->user->id, 'usertext' => '');
					if ($character['name']) {
						$newcharacter = new CharacterModel(NULL, $character);
						$post['character'] = $newcharacter->id;
						$this->_view->clear('view', $this->user->id, 'user');
						echo $post['character'];
					} else {
						$this->setnotice('user_createcharacter_namemissing', 'error');
						$this->move('thread/view/' . $this->obj->id);
					}
				}
			}
			$character = Cache::_('CharacterModel', $post['character']);
			if ($character->user->id != $this->user->id) {
				$this->setnotice('post_create_nopermission', 'error');
				$this->move('thread/view/' . $this->obj->id);
			}
			$newpost = new PostModel(NULL, $post);
			$this->obj->update(array('post__total' => ($this->obj->post__total + 1), 'post__last' => $newpost->id, 'post__last_time' => $newpost->time));
			$this->user->update(array('post__total' => ($this->user->post__total + 1)));
			$character->update(array('post__total' => ($character->post__total + 1)));
			$this->obj->board->update(array('post__total' => ($this->obj->board->post__total + 1), 'post__last' => $newpost->id));
			$this->move('thread/view/' . $this->obj->id . '/last#post' . $newpost->id);
		}
		$this->set('post', $post);
	}

	function edit($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'editpost') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'editpost') < 2) {
			$this->setnotice('post_edit_nopermission', 'error');
			$this->_view->change('view');
			exit;
		}
		$post = array('character' => $this->post('character'), 'message' => trim($this->post('message')));
		if ($post['message']) {
			$character = Cache::_('CharacterModel', $post['character']);
			if ($character->user->id != $this->user->id) {
				$this->move('thread/view/' . $this->obj->thread->id);
				exit;
			}
			if ($character->id != $this->obj->character->id) {
				$this->obj->character->update(array('post__total' => ($this->obj->character->post__total - 1)));
				$character->update(array('post__total' => ($character->post__total + 1)));
				if ($character->regdate > $this->obj->time) $character->update(array('regdate' => $this->obj->time));
			}
			$this->obj->update($post);
			$this->move('thread/view/' . $this->obj->thread->id);
		}
		$this->set('post', $post);
	}

	function delete($id = 1)
	{
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
			$newlastthreadpost = $thread->post[array_key_last($thread->post)];
			$newlastboardpost = $newlastthread->post[array_key_last($newlastthread->post)];
			$thread->update(array('post__total' => ($thread->post__total - 1), 'post__last' => $newlastthreadpost, 'post__last_time' => $newlastthreadpost->time));
			$user->update(array('post__total' => ($user->post__total - 1)));
			$thread->board->update(array('post__total' => ($thread->board->post__total - 1), 'post__last' => $newlastboardpost));
			$this->move('thread/view/' . $thread->id);
		}
	}
}
