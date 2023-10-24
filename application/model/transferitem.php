<?php
class TransferitemModel extends Model {

	protected $datatypes = array(
		'transfer' => 'parent',
		'item' => 'parent',
		'stack' => 'int'
	);
}