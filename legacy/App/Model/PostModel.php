<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;
use Legacy\Library\Class\Permission;

class PostModel extends Model
{

	protected $datatypes = array(
		'thread' => 'parent',
		'user' => 'parent',
		'character' => 'parent',
		'time' => 'int',
		'message' => 'string',
		'smilies' => 'int',
		'signature' => 'int',
		'ip' => 'string',
		'transfer' => 'children',
		'board_thread_post_edit' => 'children'
	);

	protected $permission = 'thread';

	public function deletepost()
	{
		global $user;
		if (Permission::getPermission($this, 'deletepost') == 2 || (Permission::getPermission($this, 'deletepost') == 1 && $user->id == $this->user->id)) {
			$board_thread_update = array('post__total' => ($this->thread->post__total - 1));
			$board_update = array('post__total' => ($this->thread->board->post__total - 1));
			if ($this->id == $this->thread->board->post__last->id) {
				$board_update['post__last'] = ($this->thread->post[count($this->thread->post) - 1]->id == $this->id) ? $this->thread->post[count($this->thread->post) - 2]->id : $this->thread->post[count($this->thread->post) - 1]->id;
			}
			$this->thread->update($board_thread_update);
			$this->thread->board->update($board_update);
			$this->user->update(array('post__total' => ($this->user->post__total - 1)));
			$this->delete();
			return TRUE;
		} else return FALSE;
	}
}
