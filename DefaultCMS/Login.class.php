<?php

require_once pwbdir.'/newcontroller/Component.class.php';

class Login extends Component
{
	var $state;
	var $username;
	var $password;

	function initialize(){
		$this->username =& new ValueHolder($s1 = '');
		$this->password =& new ValueHolder($s2 = '');
		$this->add_component(new Input($this->username), 'comp_username');
		$this->add_component(new Password($this->password), 'comp_password');
		$this->add_component(new ActionLink($this, 'login_do', 'Login',$params=null), 'login');
		$this->state =& new ValueHolder($s = '');
		$this->add_component(new Text($this->state), 'status');
	}

	function login_do(){
		$u = $this->username->getValue();
		$p = $this->password->getValue();
		$success =& User::login($u, $p);
		if ($success){
			$this->triggerEvent('menuChanged', $success);
			$this->state->setValue($v = 'success');
		} else {
			$this->state->setValue($v = 'failed');
			$this->password->setValue($p2 = '');
		}
	}
}

?>