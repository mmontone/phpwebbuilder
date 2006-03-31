<?php

require_once dirname(__FILE__) . '/../Controller.class.php';

class Login extends Controller
{
	function begin ($form){
		if (isset($form["Username"])) {
			User::login($form["Username"], $form["Password"]);
			if ($_SESSION[sitename]["Username"]) {
				return "Logged in";
			} else { 
				return "Error Logging in ".$this->showForm();
			}
		} 
		else return $this->showForm();
	}
	function showForm() {
		return '<form method="post" action="Action.php">' .
				'<input type="hidden" name="Controller" value="Login" />
				  <table>
				    <tr>
				      <td>Username:</td>
				      <td><input type="text" name="Username"></td>
				    </tr>
				    <tr>
				      <td>Password:</td>
				      <td><input type="password" name="Password"></td>
				    </tr>
				  </table>
				    <input type="submit" name="Submit" value="Login">
				</form>';
	}
}

?>