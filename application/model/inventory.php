<?php
class InventoryModel extends Model {

	protected $datatypes = array(
		'item' => 'parent',
		'stack' => 'int', 
		'wear' => 'string'
	);
}