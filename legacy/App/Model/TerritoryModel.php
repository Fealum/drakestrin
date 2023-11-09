<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class TerritoryModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'type' => 'int',
		'territory' => 'parent',
		'character' => 'parent',
		'area' => 'int',
		'population' => 'int',
		'settlement' => 'parent',
		'geom' => 'geom',
		'territory__children' => 'children'
	);

	protected $dataorder = array(
		'territory__children' => array('name,a', array('name' => 'LOWER(name)'))
	);
}
