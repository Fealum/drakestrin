<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class EncyclopediaModel extends Model
{

	protected $datatypes = array(
		'encyclopedia' => 'parent',
		'user' => 'parent',
		'name' => 'string',
		'title' => 'string',
		'text' => 'string',
		'sort' => 'int',
		'time' => 'int',
		'activated' => 'int',
		'encyclopedia__1' => 'children'
	);

	protected $dataorder = array(
		'encyclopedia__1' => array('sort,a;name,a', array('sort' => 'sort', 'name' => 'LOWER(name)'))
	);
}
