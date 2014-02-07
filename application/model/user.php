<?php
class UserModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'password' => 'hidden',
		'email' => 'string',
		'regemail' => 'string',
		'regdate' => 'int',
		'lastvisit' => 'int',
		'signature' => 'string',
		'birthday' => 'int',
		'avatar' => 'int',
		'interests' => 'string',
		'location' => 'string',
		'work' => 'string',
		'gender' => 'int',
		'usertext' => 'string',
		'receiveemails' => 'int',
		'invisible' => 'int',
		'style' => 'int',
		'post__total' => 'int',
		'group' => 'relation',
		'post' => 'children',
		'user_contact' => 'children',
		'company' => 'children',
		'inventory___owner' => 'mchildren',
		'permission___recipient' => 'mchildren',
		'configuration___recipient' => 'mchildren'
	);
	
	protected $dataorder = array(
		'group' => array('priority,a;name,a', array('priority' => 'priority', 'name' => 'LOWER(name)')),
		'user_contact' => array('protocol,a;contact,a', array('protocol' => 'protocol', 'contact' => 'contact'))
	);

	public function check_password($password) {
		$this->select();
		if ($password == $this->data['password']) return true;
		else return false;
	}
	
	public function return_password() {
		$this->select();
		return md5($this->data['password']);
	}
}