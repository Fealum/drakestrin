<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class BbcodeModel extends Model
{

	protected $datatypes = array(
		'pregsearch' => 'string',
		'pregreplace' => 'string',
		'params' => 'int'
	);
}
