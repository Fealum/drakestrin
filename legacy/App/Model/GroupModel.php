<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class GroupModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'acp' => 'int',
		'group__parent' => 'parent',
		'priority' => 'int',
		'user' => 'relation',
		'permission___recipient' => 'mchildren'
	);
}
