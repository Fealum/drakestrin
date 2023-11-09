<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;

class CalendarController extends Controller
{

	protected $stdaction = 'viewall';

	function std()
	{
		$this->viewall();
	}

	function viewall()
	{
	}
}
