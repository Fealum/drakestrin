<?php
class ConversationfilterModel extends Model {

	protected $datatypes = array(
		'conversation__last' => 'parent',
		'time_start' => 'int',
		'time_end' => 'int',
		'user__1' => 'parent',
		'user__2' => 'parent',
		'message_total' => 'int'
	);
}