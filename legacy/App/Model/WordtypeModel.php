<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class WordtypeModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'code' => 'string',
		'dictionary' => 'children'
	);
}
