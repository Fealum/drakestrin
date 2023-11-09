<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class ConversationModel extends Model
{

	protected $datatypes = array(
		'user__sender' => 'parent',
		'user__recipient' => 'parent',
		'view' => 'int',
		'time' => 'int',
		'message' => 'string'
	);
}
