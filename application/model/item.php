<?php
class ItemModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'description' => 'string',
		'img' => 'int',
		'weight' => 'int',
		'unit' => 'string'
	);
}