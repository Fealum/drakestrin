<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class Labour_activeModel extends Model
{

	protected $datatypes = array(
		'company_worker' => 'parent',
		'labour' => 'parent',
		'since' => 'int',
		'until' => 'int',
		'prodas' => 'int',
		'quantity' => 'int',
		'instances' => 'int',
		'nextinsta' => 'int'
	);
}
