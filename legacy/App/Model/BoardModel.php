<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class BoardModel extends Model
{

	protected $datatypes = array(
		'board' => 'parent',
		'name' => 'string',
		'description' => 'string',
		'sort' => 'int',
		'cat' => 'int',
		'thread__total' => 'int',
		'post__total' => 'int',
		'post__last' => 'parent',
		'board__1' => 'children',
		'thread' => 'children',
		'permission___subject' => 'mchildren'
	);

	protected $dataorder = array(
		'board__1' => array('sort,a;name,a', array('sort' => 'sort', 'name' => 'LOWER(name)')),
		'thread' => array('important,d;post__last_time,d', array('important' => 'important', 'post__last_time' => 'post__last_time'))
	);

	protected $permission = 'board';
}
