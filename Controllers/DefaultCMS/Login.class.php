<?php

require_once dirname(__FILE__) . '/../Controller.class.php';

class Login extends Component {
	var $success = false;
	function declare_actions(){
		return array("login_do");
	}
	function login_do ($form){
		$this->success =& User::login($form["Username"], $form["Password"]);
	}
	function render_on(&$html) {
		if ($this->success) {
			$html->text('');
		} else { 
			$html->begin_form_for_action("login_do");
			$html->text('<table>
					    <tr>
					      <td>Username:</td>
					      <td><input type="text" name="p_Username"></td>
					    </tr>
					    <tr>
					      <td>Password:</td>
					      <td><input type="password" name="p_Password"></td>
					    </tr>
					  </table>');
			$html->text('<input type="submit" name="Submit" value="Login">');				  
		    $html->text("</form>");
		}
	}
}

?>