<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class TransferitemModel extends Model
{

	protected $datatypes = array(
		'transfer' => 'parent',
		'item' => 'parent',
		'stack' => 'int'
	);
}
