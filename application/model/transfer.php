<?php
class TransferModel extends Model {

	protected $datatypes = array(
		'post' => 'parent',
		'table__sender' => 'int',
		'sender' => 'mparent',
		'table__recipient' => 'int',
		'recipient' => 'mparent',
		'transferitem' => 'children'
	);
}