<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class LabourModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'type' => 'int',
		'duration' => 'int',
		'workload' => 'int',
		'labour_component' => 'children'
	);
}
