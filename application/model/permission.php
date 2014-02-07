<?php
class PermissionModel extends Model {

	protected $datatypes = array(
		'table__recipient' => 'int',
		'recipient' => 'mparent',
		'table__subject' => 'int',
		'subject' => 'mparent',
		'permit' => 'parent',
		'value' => 'int'
	);
}