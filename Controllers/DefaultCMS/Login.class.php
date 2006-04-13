<?php

require_once dirname(__FILE__) . '/../Controller.class.php';

class Login extends Component {
	var $success = false;
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
	}
	function login_do ($form){
		$u =& $this->component_at("username");
		$p =& $this->component_at("password");
		$this->success =& User::login($u->value, $p->value); 
		if ($this->success){
			$this->triggerEvent('menuChanged');
			$this->add_component(new Text('success'), "status");
		} else {
			$this->add_component(new Text('failed'), "status");
		}
	}
	function render_on(&$html) {
		if ($this->success) {
			$html->text('');
		} else { 
		}
	}
}

?>