<?php
class Session {

	protected $name;

	function __construct($name = 'session', $restore = TRUE) {
		$this->name = $name;
		session_name($name);
		session_start();
		if ($restore == TRUE && !isset($_SESSION[$this->name]) && isset($_COOKIE['savecookie_'.$this->name])) {
			$cookie = json_decode($_COOKIE['savecookie_'.$this->name], TRUE);
			$_SESSION[$this->name] = $cookie;
			$this->__set('ip', $_SERVER['REMOTE_ADDR']);
		}
	}
	
	function check() {
		if (is_null($this->__get('ip'))) {
			$this->__set('ip', $_SERVER['REMOTE_ADDR']);
			return TRUE;
		}
		elseif ($this->__get('ip') != $_SERVER['REMOTE_ADDR']) {
			$this->destroy();
			return FALSE;
		}
		else return TRUE;
	}
	
	function savecookie() {
		setcookie('savecookie_'.$this->name, json_encode($_SESSION[$this->name]), (time() + 365 * 86400), '/');
	}

	function __set($setting, $value) {
		if ($value == NULL) unset($_SESSION[$this->name][$setting]);
		else $_SESSION[$this->name][$setting] = $value;
	}

	function __get($setting) {
		if (isset($_SESSION[$this->name][$setting]) && !empty($_SESSION[$this->name][$setting])) return $_SESSION[$this->name][$setting];
		else return NULL;
	}
	
	function destroy() {
		setcookie($this->name, "", time()-3600, '/');
		setcookie('savecookie_'.$this->name, "", time()-3600, '/');
		session_unset();
		session_destroy();
	}
}