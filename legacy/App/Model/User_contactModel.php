<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class User_contactModel extends Model
{

	protected $datatypes = array(
		'user' => 'parent',
		'protocol' => 'parent',
		'contact' => 'string'
	);

	protected $permission = 'user';
}
