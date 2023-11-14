<?php

namespace Legacy\Library\Class;

use Legacy\App\Model\OnlineModel;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{

	protected $_model, $_controller, $_view, $_action, $stdaction = 'std', $setonline = TRUE, $cacheid = NULL, $move = FALSE;
	public $session, $online, $user = NULL, $obj, $notice = array(), $config;

	function __construct($model, $controller, $action, $config, $tables)
	{
		date_default_timezone_set(config('app.timezone'));
		if ($action == 'std') $action = $this->stdaction;
		$this->_model = $model;
		$this->_controller = $controller;
		$this->_view = new View($controller, $action);
		$this->_action = $action;

		$this->config = $config;
		$this->set('config', $config);

		// Initialize session
		if (Auth::check()) {
			$this->user = Cache::_('UserModel', Auth::id());
			$this->set('user', $this->user);
			$newconv = new _list('conversation', '`view` = 0 AND `user__recipient` = ' . $this->user->id);
			$this->set('newconv', $newconv);
			// Initialize online
			if ($this->setonline == TRUE) {
				$this->online = new OnlineModel();
				if (!$this->online->id_from_unique('user', $this->user->id)) $this->online = new OnlineModel(NULL, array('time' => time(), 'ip' => request()->ip(), 'user' => $this->user->id, 'browser' => request()->server('HTTP_USER_AGENT'), 'controller' => $this->_controller, 'action' => $this->_action));
				elseif ($this->user) $this->online->update(array('time' => time(), 'ip' => request()->ip(), 'user' => $this->user->id, 'browser' => request()->server('HTTP_USER_AGENT'), 'controller' => $this->_controller, 'action' => $this->_action, 'table__location' => NULL, 'location' => NULL));
			}
		}

		$this->session = $this->session ?? new Session();

		// Delete old onlines
		$deleteonlines = new _list('online', '`time` < (UNIX_TIMESTAMP(now()) - ' . $config->timeout . ')');
		if (!is_null($deleteonlines->data)) foreach ($deleteonlines->data as $cur) {
			$cur->user->update(array('lastvisit' => $cur->time));
			$cur->delete();
		}

		// Get onlines
		$online = new _list('online', NULL, array('time,d', array('time' => 'time')));
		$this->set('online', $online);

		// Initialize permissions and configurations
		Permission::setPermissions($this->user);
		$this->set('permission', Permission::getInstance());
		Configuration::setConfigurations($this->user);
		$this->set('configuration', Configuration::getInstance());

		// Initialize BBCode
		//$bbcodes = new _list('bbcode');
		$bbcodes = BBCode::getInstance();
		$this->set('bbcodes', $bbcodes);

		// Initialize emoticons
		$emoticons = new _list('emoticon');
		$this->set('emoticons', $emoticons->data);
	}

	function post($var, $else = NULL)
	{
		return request($var, $else);
	}

	function onlinelocation($table, $location)
	{
		if ($this->user) $this->online->update(array('table__location' => (int)$table, 'location' => (int)$location));
	}

	function setnotice($notice, $type = 'info', $vars = NULL)
	{
		if ($notice != '') {
			$pusharray = $this->session->notice;
			$this->session->notice = is_array($pusharray)
				? array_push($pusharray, array('notice' => $notice, 'type' => $type, 'vars' => $vars))
				: array(array('notice' => $notice, 'type' => $type, 'vars' => $vars));
		}
	}

	function move($path)
	{
		$this->move = TRUE;
		header('Location: ' . $this->config->url . '/' . $path);
		exit;
	}

	function set($name, $value = NULL)
	{
		if (is_array($name)) foreach ($name as $key => $value) $this->set($key, $value);
		$this->_view->set($name, $value);
	}

	function cacheid($cacheid = '0')
	{
		$this->cacheid = $cacheid;
	}

	function __destruct()
	{
		if (!$this->move) {
			$this->set('notice', $this->session->notice);
			$this->session->notice = NULL;
			$this->session->savecookie();
			$this->_view->output($this->cacheid);
		}
	}
}
