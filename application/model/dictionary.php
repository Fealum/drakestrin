<?php
class DictionaryModel extends Model {

	protected $datatypes = array(
		'language' => 'parent',
		'wordtype' => 'parent',
		'word' => 'string',
		'dictionarykey___from' => 'children',
		'dictionarykey___to' => 'children',
		'val' => 'int'
	);
}