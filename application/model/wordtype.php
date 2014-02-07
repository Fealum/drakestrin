<?php
class WordtypeModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'code' => 'string',
		'dictionary' => 'children'
	);
}