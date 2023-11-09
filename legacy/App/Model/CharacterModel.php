<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class CharacterModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'post__total' => 'int',
		'regdate' => 'int',
		'signature' => 'string',
		'birthday' => 'int',
		'avatar' => 'int',
		'interests' => 'string',
		'location' => 'string',
		'work' => 'string',
		'gender' => 'int',
		'usertext' => 'string',
		'user' => 'parent',
		'company' => 'children',
		'territory' => 'children',
		'inventory___owner' => 'mchildren'
	);
}
