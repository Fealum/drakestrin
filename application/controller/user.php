<?php
class UserController extends Controller {

	protected $stdaction = 'viewall';

	function std($order = 'postsperday,d;name,a', $page = 1) {
		$this->viewall($order, $page);
	}

	function viewall($order = 'postsperday,d;name,a', $page = 1) {
		$this->obj = new _list('user', NULL, array($order, array('post' => 'post__total', 'name' => 'LOWER(name)', 'postsperday' => '(post__total / ((UNIX_TIMESTAMP() - regdate)  / 86400))', 'regdate' => 'regdate', 'lastvisit' => 'lastvisit')));
		$this->set('page', $page);
		$this->set('order', $order);
		$this->set('obj', $this->obj);
	}
	
	function view($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(0, $this->obj->id);
		$this->set('obj', $this->obj);
		$this->cacheid($this->obj->id);
	}
	
	function edit($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(0, $this->obj->id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'edituser') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'edituser') < 2) {
			$this->setnotice('user_edit_nopermission', 'error');
			$this->move('user/view/'.$this->obj);
		}
		$edituser = array('usertext' => $this->post('usertext'), 'location' => $this->post('location'), 'interests' => $this->post('interests'), 'work' => $this->post('work'));
		if ($this->post('edit')) {
			if ($this->post('changeavatar') == 1 && isset($_FILES['avatar']['tmp_name'])) {
				$avatar = new Upload($_FILES['avatar']);
				if ($avatar->uploaded) {
					$avatar->file_max_size = $this->config->avatarsize;
					$avatar->file_new_name_body = $this->user->id;
					$avatar->file_auto_rename = false;
					$avatar->file_overwrite = true;
					$avatar->image_convert = 'jpg';
					$avatar->jpeg_quality = 90;
					$avatar->image_x = 140;
					$avatar->image_y = 160;
					$avatar->image_resize = true;
					$avatar->image_ratio_no_zoom_in = true;
					$avatar->process(createPath(array('img', 'user_avatar.id/')));
					if (!$avatar->processed) echo 'error : ' . $avatar->error;
					$avatar->file_new_name_body = $this->user->id;
					$avatar->file_auto_rename = false;
					$avatar->file_overwrite = true;
					$avatar->image_convert = 'jpg';
					$avatar->jpeg_quality = 90;
					$avatar->image_x = 60;
					$avatar->image_y = 60;
					$avatar->image_resize = true;
					$avatar->image_ratio_crop = true;
					$avatar->process(createPath(array('img', 'user_avatar.id', 'thumb/')));
					if (!$avatar->processed) echo 'error : ' . $avatar->error;
					$avatar->clean();
					$edituser['avatar'] = '1';
				}
			}
			$this->obj->update($edituser);
			$this->_view->clear('view', $this->obj->id);
			$this->setnotice('user_edit', 'success');
			$this->move('user/view/'.$this->obj);
		}
		$this->set('edituser', $edituser);
	}
	
	function createcontact($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		if ((Permission::getPermission($this->obj, 'edituser') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'edituser') < 2) {
			$this->setnotice('user_edit_nopermission', 'error');
			$this->move('user/view/'.$this->obj);
		}
		$user_contact = array('user' => $this->obj->id, 'protocol' => $this->post('protocol'), 'contact' => $this->post('contact'));
		if ($user_contact['user'] && $user_contact['protocol'] && $user_contact['contact']) {
			new User_contactModel(NULL, $user_contact);
			$this->_view->clear('view', $this->obj->id);
			$this->setnotice('user_edit', 'success');
			$this->move('user/view/'.$this->obj);
		}
		$this->set('obj', $this->obj);
		$protocols = new _list('protocol');
		$this->set('protocols', $protocols);
	}
	
	function editcontact($id = 1) {
		$this->obj = Cache::_('User_contactModel', $id);

		if ((Permission::getPermission($this->obj, 'edituser') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'edituser') < 2) {
			$this->setnotice('user_edit_nopermission', 'error');
			$this->move('user/view/'.$this->obj->user);
		}
		$user_contact = array('protocol' => $this->post('protocol'), 'contact' => $this->post('contact'));
		if ($user_contact['protocol'] && $user_contact['contact']) {
			$this->obj->update($user_contact);
			$this->_view->clear('view', $this->obj->user->id);
			$this->setnotice('user_edit', 'success');
			$this->move('user/view/'.$this->obj->user);
		}
		$this->set('obj', $this->obj);
		$protocols = new _list('protocol');
		$this->set('protocols', $protocols);
	}
	
	function deletecontact($id = 1) {
		$this->obj = Cache::_('User_contactModel', $id);
		$this->set('obj', $this->obj);
		if ((Permission::getPermission($this->obj, 'edituser') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'edituser') < 2) {
			$this->setnotice('user_edit_nopermission', 'error');
			$this->move('user/view/'.$this->obj->user);
		}
		if ($this->post('delete') == 1) {
			$moveto = $this->obj->user->id;
			$this->obj->delete();
			$this->_view->clear('view', $moveto);
			$this->setnotice('user_edit', 'success');
			$this->move('user/view/'.$moveto);
		}
	}
}