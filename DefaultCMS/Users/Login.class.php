<?php

class Login extends Component
{
	var $state;
	var $username;
	var $password;

	function initialize(){
		$this->username =& new ValueHolder($s1 = '');
		$this->password =& new ValueHolder($s2 = '');
		$this->addComponent(new Input($this->username), 'comp_username');
		$this->addComponent(new Password($this->password), 'comp_password');
		$this->addComponent(new ActionLink($this, 'login_do', 'Login',$params=null), 'login');
		$this->state =& new ValueHolder($s = '');
		$this->addComponent(new Text($this->state), 'status');
	}

	function login_do() {
		$u = $this->username->getValue();
		$p = $this->password->getValue();
		$success =& User::login($u, $p);
		if ($success){
			$this->triggerEvent('logged', $success);
			$this->triggerEvent('menuChanged', $success);
			$this->state->setValue((Translator::Translate('login successful')));
		} else {
			$this->state->setValue((Translator::Translate('login failed')));
			$this->password->setValue($p2 = '');
		}
	}
}

?>