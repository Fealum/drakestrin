<?php
class CompanyModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'description' => 'string',
		'text' => 'string',
		'url' => 'string',
		'user' => 'parent',
		'thread' => 'parent',
		'geldstand' => 'string',
		'company_worker' => 'children',
		'inventory___owner' => 'mchildren',
	);
}