<?php
class GroupController extends Controller {

	protected $stdaction = 'viewall';

	function std($order = 'postsperday,d;name,a', $page = 1) {
		$this->viewall($order, $page);
	}

	function viewall($order = 'postsperday,d;name,a', $page = 1) {
		$this->obj = new _list('user', NULL, array($order, array('post' => 'post__total', 'name' => 'LOWER(name)', 'postsperday' => '(post__total / ((UNIX_TIMESTAMP() - regdate)  / 86400))', 'regdate' => 'regdate')));
		$this->set('page', $page);
		$this->set('order', $order);
		$this->set('obj', $this->obj);
	}
	
	function view($id = 1, $page = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('page', $page);
		$this->set('obj', $this->obj);
		$this->cacheid($this->obj->id.'|'.$page);
	}
	
	function edit($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
			if ((Permission::getPermission($this->obj, 'edituser') < 1 && $this->user->id != $this->obj->user->id) && Permission::getPermission($this->obj, 'edituser') < 2) {
			$this->notice[] = 'user_edit_nopermission';
			$this->_view->change('view');
			exit;
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
					$avatar->image_ratio_no_zoom_out = true;
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
				}
			}
			$this->obj->update($edituser);
			$this->_view->clearCache('view', $this->obj->id);
			header('Location: '.$this->config->url.'/user/view/'.$this->obj->id);
		}
		$this->set('edituser', $edituser);
	}
}