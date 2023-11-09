<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class CompanyModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'type' => 'int',
		'character' => 'parent',
		'description' => 'string',
		'text' => 'string',
		'territory' => 'parent',
		'thread' => 'parent',
		'url' => 'string',
		'company_worker' => 'children',
		'inventory___owner' => 'mchildren',
	);
}
