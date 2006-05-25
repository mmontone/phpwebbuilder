<?php

//require_once("Controller.class.php");

/**
 * Controller for editing the data for the user that's logged in
 */

class MyUserController extends Controller {
	var $roleid = 1;
	function permissionNeeded () {
		return "User";
	}
	function begin($form) {
		$ret ="";
		$ret .=$this->saveValues($form);
		$ret .=$this->showOptions($form);
		return $ret;
	}
	function saveValues($form) {

	}
	function showOptions($form) {
		$u = new User;
		$u->setID($_SESSION[sitename]["id"]);
		$u->load();
		$view = new HTMLTableEditView();
		$view = $view->viewFor($u);
		return $view->show();
	}
}

?>
