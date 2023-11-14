<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;
use Legacy\Library\Class\Cache;
use Legacy\App\Model\UserModel;
use Legacy\App\Model\ValidemailModel;

class RegisterController extends Controller
{

	protected $stdaction = 'std';

	function std()
	{
	}

	function validate()
	{
		$email = $this->post('email');
		$this->set('registeremail', $email);
		if ($email == NULL) {
			$this->setnotice('register_noemail', 'error');
			$this->_view->change('std');
			exit;
		}
		if (!validEmail($email)) {
			$this->setnotice('register_invalidemail', 'error');
			$this->_view->change('std');
			exit;
		}
		$testuser = new UserModel;
		if ($testuser->id_from_unique('email', $email)) {
			$this->setnotice('register_emailalreadyinuse', 'error');
			$this->_view->change('std');
			exit;
		}
		$testvalidemail = new ValidemailModel;
		if ($testvalidemail->id_from_unique('email', $email)) {
			$this->setnotice('register_emailalreadyinvalidation', 'error');
			$this->_view->change('std');
			exit;
		}
		$validuntil = time() + $this->config->emailvalidfor;
		$key = md5($this->config->salt . $email . $this->config->salt . $validuntil . $this->config->salt);
		$validemail = new ValidemailModel(NULL, array('email' => $email, 'validuntil' => $validuntil));
		$this->set('registerkey', $key);
		mail($email, $this->_view->getdisplay('register', 'validate_mailsubject'), $this->_view->getdisplay('register', 'validate_mailcontent'), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=utf-8' . "\r\nFrom: " . $this->config->email);
	}

	function registration($email = FALSE, $key = FALSE)
	{
		if (!$email || !$key) {
			$this->setnotice('register_emailorkeymissing', 'error');
			$this->_view->change('std');
			exit;
		}
		$this->set(array('registeremail' => $email, 'registerkey' => $key));

		// Abgelaufene Validemails löschen
		$deletevalidemails = new _list('validemail', '`validuntil` < UNIX_TIMESTAMP(now())');
		if (!is_null($deletevalidemails->data)) foreach ($deletevalidemails->data as $cur) $cur->delete();

		$validemail = new ValidemailModel;
		if (!$validemail->id_from_unique('email', $email)) {
			$this->setnotice('register_emailnotinsystem', 'error');
			$this->_view->change('std');
			exit;
		}
		if ($key != md5($this->config->salt . $email . $this->config->salt . $validemail->validuntil . $this->config->salt)) {
			$this->setnotice('register_invalidkey', 'error');
			$this->_view->change('std');
			exit;
		}
		$userdata['password'] = $this->post('password');
		$userdata['name'] = ($this->post('name') !== NULL) ? trim($this->post('name')) : NULL;
		if ($this->post('termsofservice')) $termsofservice = $this->post('termsofservice');
		if ($this->post('passwordcheck')) $passwordcheck = $this->post('passwordcheck');
		$this->set(array('registername' => $userdata['name'], 'registerpassword' => $userdata['password']));
		if (isset($userdata['name']) || isset($userdata['password'])) {
			// check error cases
			$checknotice = $this->notice;
			if (!isset($userdata['name'])) $this->setnotice('register_noname', 'error');
			$testuser = new UserModel;
			if (isset($userdata['name']) && $testuser->id_from_unique('name', $userdata['name'])) $this->setnotice('register_namealreadyinuse', 'error');
			if (!isset($userdata['password'])) $this->setnotice('register_nopassword', 'error');
			if (isset($userdata['password']) && $userdata['password'] != $passwordcheck) $this->setnotice('register_passwordnotidentical', 'error');
			if (!isset($termsofservice)) $this->setnotice('register_notermsofservice', 'error');
			// if no error case was found, create user
			if ($checknotice == $this->notice) {
				$userdata['email'] = $email;
				$userdata['password'] = password_hash($userdata['password'], PASSWORD_DEFAULT);
				$userdata['regemail'] = $email;
				$userdata['regdate'] = time();
				$userdata['lastvisit'] = $userdata['regdate'];
				$userdata['lastactivity'] = $userdata['regdate'];
				$userdata['usertext'] = '';
				$user = new UserModel(NULL, $userdata);
				$standardgroup = Cache::_('GroupModel', 1);
				$user->createrelation('group', $standardgroup);
				$validemail->delete();
				$this->session->userid = $user->id;
				$this->session->userpw = $userdata['password'];
				$this->move('register/welcome');
			}
		}
	}

	function welcome()
	{
	}
}
