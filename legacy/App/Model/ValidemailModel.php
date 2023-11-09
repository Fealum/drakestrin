<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class ValidemailModel extends Model
{
	protected $datatypes = array(
		'email' => 'string',
		'validuntil' => 'int'
	);
}
