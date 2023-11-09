<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class ThreadfilterModel extends Model
{

	protected $datatypes = array(
		'board' => 'parent',
		'thread' => 'parent',
		'name' => 'string',
		'post__total' => 'int',
		'post__first' => 'parent',
		'post__first_user' => 'parent',
		'post__first_character' => 'parent',
		'post__first_time' => 'int',
		'post__last' => 'parent',
		'post__last_user' => 'parent',
		'post__last_character' => 'parent',
		'post__last_time' => 'int',
		'views' => 'int',
		'important' => 'int',
		'user' => 'parent',
		'character' => 'parent',
		'posttime' => 'int',
		'message' => 'string'
	);

	protected $permission = 'board';
}
