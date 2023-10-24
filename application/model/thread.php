<?php
class ThreadModel extends Model {

	protected $datatypes = array(
		'board' => 'parent',
		'name' => 'string',
		'post__total' => 'int',
		'post__first' => 'parent',
		'post__first_time' => 'int',
		'post__last' => 'parent',
		'post__last_time' => 'int',
		'views' => 'int',
		'important' => 'int',
		'company' => 'children',
		'post' => 'children'
	);
	
	protected $permission = 'board';
}