<?php
class EncyclopediaModel extends Model {

	protected $datatypes = array(
		'encyclopedia' => 'parent',
		'user' => 'parent',
		'name' => 'string',
		'title' => 'string',
		'text' => 'string',
		'sort' => 'int',
		'time' => 'int',
		'activated' => 'int',
		'encyclopedia__1' => 'children'
	);
}