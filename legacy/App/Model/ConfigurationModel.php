<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class ConfigurationModel extends Model
{

	protected $datatypes = array(
		'table__recipient' => 'int',
		'recipient' => 'mparent',
		'table__subject' => 'int',
		'subject' => 'mparent',
		'setting' => 'parent',
		'value' => 'int'
	);
}
