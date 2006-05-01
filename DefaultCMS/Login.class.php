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
		$status =& $this->getComponent('status');
		if ($success){
			$this->triggerEvent('menuChanged', $success);
			//$status->setText('success');
			//$this->state = 'success';
			$this->setState('success');
		} else {
			//$status->setText('failed');
			//$this->state = 'failed';
			$this->setState('failed');
		}
	}

	function setState($text) {
		$this->state = $text;
		$status =& $this->getComponent('status');
		$status->changed();
	}
}

?>