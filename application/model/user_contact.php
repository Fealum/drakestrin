<?php
class User_contactModel extends Model {

	protected $datatypes = array(
		'user' => 'parent',
		'protocol' => 'parent',
		'contact' => 'string'
	);
	
	protected $permission = 'user';
}