<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class TransferModel extends Model
{

	protected $datatypes = array(
		'post' => 'parent',
		'table__sender' => 'int',
		'sender' => 'mparent',
		'table__recipient' => 'int',
		'recipient' => 'mparent',
		'transferitem' => 'children'
	);
}
