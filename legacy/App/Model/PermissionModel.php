<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class PermissionModel extends Model
{

	protected $datatypes = array(
		'table__recipient' => 'int',
		'recipient' => 'mparent',
		'table__subject' => 'int',
		'subject' => 'mparent',
		'permit' => 'parent',
		'value' => 'int'
	);
}
