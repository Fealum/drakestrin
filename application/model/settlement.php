<?php
class SettlementModel extends Model {

	protected $datatypes = array(
		'name' => 'string',
		'population' => 'int',
		'priority' => 'int',
		'geom' => 'geom',
		'territory' => 'children'
	);
}