<?php

require_once pwbdir.'/newcontroller/Component.class.php';

class Login extends Component {
	function initialize(){
			$this->add_component(new Input("Username"), "username");
			$this->add_component(new Password("Password"), "password");
			$n = null;
			$this->add_component(new ActionLink($this, 'login_do', "Login", $n), "login");
			$this->add_component(new Text(''), "status");
	}
	function login_do(){
		$success =& User::login($this->username->value, $this->password->value);
		if ($success){
			$this->triggerEvent('menuChanged', $success);
			$this->status->setText('success');
		} else {
			$this->status->setText('failed');
		}
	}
}

?>