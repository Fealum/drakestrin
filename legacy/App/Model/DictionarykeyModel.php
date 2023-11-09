<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class DictionarykeyModel extends Model
{

	protected $datatypes = array(
		'dictionary__from' => 'parent',
		'dictionary__to' => 'parent'
	);
}
