<?php

require_once pwbdir.'/newcontroller/Component.class.php';

class Login extends Component {
	var $state = '';
	function initialize(){
			$this->add_component(new Input($u = null), 'username');
			$this->add_component(new Password($p = null), 'password');
			$n = null;
			$this->add_component(new ActionLink($this, 'login_do', 'Login', $n), 'login');
			$this->add_component(new Text($this->state), 'status');
	}
	function login_do(){
		$success =& User::login($this->username->value, $this->password->value);
		if ($success){
			$this->triggerEvent('menuChanged', $success);
			$this->setState('success');
		} else {
			$this->setState('failed');
		}
	}

	function setState($text) {
		$this->state = $text;
		$this->status->changed();
	}
}

?>