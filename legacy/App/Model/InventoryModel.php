<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class InventoryModel extends Model
{

	protected $datatypes = array(
		'item' => 'parent',
		'stack' => 'int',
		'wear' => 'string',
		'table__owner' => 'int',
		'owner' => 'mparent',
	);

	public function makeunitary($stack = NULL)
	{
		if ($stack == NULL) $stack = $this->stack;
		return $this->item->makeunitary($stack);
	}

	public function undounitary($stack)
	{
		return $this->item->undounitary($stack);
	}
}
