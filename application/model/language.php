<?php
class LanguageModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'code' => 'string',
		'dictionary' => 'children'
	);
}