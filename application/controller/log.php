<?php
class LogController extends Controller {

	protected $stdaction = 'std';

	function std() {
	}

	function in() {
		if (is_null($this->user)) {
			$email = $this->post('email');
			$password = $this->post('password');
			if (!is_null($email) && isset($password)) {
				$user = new UserModel;
				if ($user->id_from_unique('email', $email)) {
					if ($user->check_password(md5($password))) {
						$this->session->userid = $user->id;
						$this->session->userpw = md5($password);
						$this->setnotice('log_in', 'success');
						$this->move('board');
					}
					else {
						$this->setnotice('log_wrongpassword', 'error');
						$this->_view->change('std');
					}
				}
				else {
					$this->set('logemail', $email);
					$this->setnotice('log_wrongemail', 'error');
					$this->_view->change('std');
				}
			}
			else $this->_view->change('std');
		}
	}
	
	function out() {
		$this->session->userid = NULL;
		$this->session->userpw = NULL;
		$this->online->delete();
		$this->user->update(array('lastvisit' => $this->online->time));
		$this->setnotice('log_out', 'success');
		$this->move('board');
	}
	
	function forgotpw() {
		if (is_null($this->user)) {
			$email = $this->post('email');
			if (!is_null($email)) {
				$user = new UserModel;
				if ($user->id_from_unique('email', $email)) {
					$key = md5($this->config->salt.$email.$this->config->salt.$user->return_password().$this->config->salt);
					$this->set('logkey', $key);
					$this->set('logemail', $email);
					mail($email, $this->_view->getdisplay('log', 'forgotpw_mailsubject'), $this->_view->getdisplay('log', 'forgotpw_mailcontent'), 'MIME-Version: 1.0'."\r\n".'Content-type: text/html; charset=utf-8' . "\r\nFrom: ".$this->config->email);
					$this->setnotice('log_emailsent', 'success');
				}
				else {
					$this->set('logemail', $this->post('email'));
					$this->setnotice('log_wrongemail', 'error');
					$this->_view->change('std');
				}
			}
		}
	}
	
	function newpw($email = FALSE, $key = FALSE) {
		if (!$email || !$key) {
			$this->setnotice('log_emailorkeymissing', 'error');
			$this->_view->change('std');
			exit;
		}
		$this->set(array('logemail' => $email, 'logkey' => $key));
		$user = new UserModel;
		if (!$user->id_from_unique('email', $email)) {
			$this->setnotice('log_emailnotinsystem', 'error');
			$this->_view->change('std');
			exit;
		}
		if ($key != md5($this->config->salt.$email.$this->config->salt.$user->return_password().$this->config->salt)) {
			$this->setnotice('log_invalidkey', 'error');
			$this->_view->change('std');
			exit;
		}
		$password = $this->post('password');
		$passwordcheck = $this->post('passwordcheck');
		if (isset($password)) {
			if ($password != $passwordcheck) {
				$this->setnotice('log_passwordnotidentical', 'error');
				exit;
			}
			$user->update(array('password' => $password));
			$this->setnotice('log_passwordupdated', 'success');
			$this->_view->change('std');
		}
	}
}