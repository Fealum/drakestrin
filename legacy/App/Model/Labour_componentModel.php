<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class Labour_componentModel extends Model
{

	protected $datatypes = array(
		'labour' => 'parent',
		'item' => 'parent',
		'quantity' => 'int',
		'type' => 'int'
	);
}
