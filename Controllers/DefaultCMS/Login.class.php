<?php

require_once dirname(__FILE__) . '/../Controller.class.php';

class Login extends Component {
	function declare_actions(){
		return array("login_do");
	}
	function initialize(){
			$this->add_component(new Text('<table><tr><td>Username:</td><td>'));
			$this->add_component(new Input("Username"), "username");
			$this->add_component(new Text('</td></tr><tr><td>Password:</td><td>'));
			$this->add_component(new Password("Password"), "password");
	        $this->add_component(new Text('</td></tr></table>'));  
			$this->add_component(new ActionLink($this, 'login_do', "Login"), "login");
			$this->add_component(new Text(''), "status");
	}
	function login_do(){
		/*echo "<br/>loggin in with ".$this->username->value." and ".$this->password->value;
		foreach  ($this->username as $n=>$i) {
			echo ("<br/>".$n ." " .$i);
		}*/
		$success =& User::login($this->username->value, $this->password->value); 
		if ($success){
			$this->triggerEvent('menuChanged');
			$this->status->setText('success');
		} else {
			$this->status->setText('failed');
		}
	}
}

?>