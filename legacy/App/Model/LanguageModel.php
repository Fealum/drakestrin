<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class LanguageModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'code' => 'string',
		'dictionary' => 'children'
	);
}
