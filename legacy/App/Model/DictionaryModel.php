<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class DictionaryModel extends Model
{

	protected $datatypes = array(
		'language' => 'parent',
		'wordtype' => 'parent',
		'word' => 'string',
		'dictionarykey___from' => 'children',
		'dictionarykey___to' => 'children',
		'val' => 'int'
	);
}
