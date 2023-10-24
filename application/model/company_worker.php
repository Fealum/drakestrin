<?php
class Company_workerModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'type' => 'int',
		'company' => 'parent',
		'hired' => 'int',
		'paid' => 'int',
		'labour_active' => 'children'
	);
}