<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class OnlineModel extends Model
{
	protected $datatypes = array(
		'time' => 'int',
		'ip' => 'string',
		'user' => 'parent',
		'browser' => 'string',
		'controller' => 'string',
		'action' => 'string',
		'table__location' => 'int',
		'location' => 'mparent'
	);
}
