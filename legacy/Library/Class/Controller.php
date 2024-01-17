<?php

namespace Legacy\Library\Class;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View as BladeView;
use App\Models\Online;

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
		}

		$this->session = $this->session ?? new Session();

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
		if (Auth::check()) {
			Online::updateOrCreate(
				['user' => Auth::id()],
				[
					'table__location' => (int)$table,
					'location' => (int)$location,
				]
			);
			BladeView::share('online', Online::all());
		}
	}

	function setnotice($notice, $type = 'info', $vars = NULL)
	{
		$messages = session('flash_messages', []);
		$messages[] = [
			'level' => $type,
			'content' => view('notifications.' . $notice, $vars)->render(),
		];

		session()->flash('flash_messages', $messages);
	}

	function move($path)
	{
		$this->move = TRUE;
		redirect($path);
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
			$this->session->savecookie();
			$this->_view->output($this->cacheid);
		}
	}
}
