<?php
class ThreadModel extends Model {

	protected $datatypes = array(
		'board' => 'parent',
		'name' => 'string',
		'time' => 'int',
		'post__total' => 'int',
		'post__last' => 'parent',
		'post__last_time' => 'int',
		'views' => 'int',
		'important' => 'int',
		'company' => 'children',
		'post' => 'children'
	);
	
	protected $permission = 'board';
}