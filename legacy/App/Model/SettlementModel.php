<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class SettlementModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'population' => 'int',
		'priority' => 'int',
		'geom' => 'geom',
		'territory' => 'children'
	);
}
