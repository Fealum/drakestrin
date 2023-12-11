<?php

namespace Legacy\Library\Class;

use \Smarty;
use Illuminate\Support\Facades\Blade;
use Legacy\Library\Class\Permission;

class View extends Smarty
{

	protected $_controller, $_action;

	function __construct($controller, $action)
	{
		parent::__construct();

		$this->setTemplateDir(createPath(array('application', 'view')));
		$this->setCompileDir(createPath(array('public', 'templates_c')));
		$this->setCacheDir(createPath(array('public', 'cache')));

		$this->addPluginsDir(createPath(array('plugins', 'smarty')));

		$this->_controller = $controller;
		$this->_action = $action;
	}

	function change($action)
	{
		$this->_action = $action;
	}

	function set($name, $value)
	{
		parent::assign($name, $value);
	}

	function output($cacheid = NULL, $cachetime = 86400)
	{
		if ($cacheid) {
			$this->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->setCacheLifetime($cachetime);
		}
		// parent::display(createPath(array('App', 'View', 'standard', $this->_controller, $this->_action . '.htm')), $cacheid);
		$smartyView = parent::fetch(createPath(array('App', 'View', 'standard', $this->_controller, $this->_action . '.htm')), $cacheid);
		echo Blade::render($smartyView);
	}

	function clear($action, $cacheid = NULL, $controller = NULL)
	{
		parent::clearCache(createPath(array('App', 'View', 'standard', ($controller != NULL) ? $controller : $this->_controller, $action . '.htm')), $cacheid);
	}

	function getdisplay($controller, $action)
	{
		return parent::fetch(createPath(array('App', 'View', 'standard', $controller, $action . '.htm')));
	}
}
