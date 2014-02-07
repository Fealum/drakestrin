<?php
class ValidemailModel extends Model {
	protected $datatypes = array(
		'email' => 'string',
		'validuntil' => 'int'
	);
}