<?
//require_once dirname(__FILE__).'/../Application/Component.class.php';

class Controller //extends Component
{
	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
	function myForm($name, $content) {
		$ret .= "<form action=\"Action.php?Controller=" . get_class($this) . "";
		$ret .= "\" method=\"POST\" name=\"$name\">" . $content;
		$ret .= "<input name=\"exec$name\" type=\"submit\" /></form>";
		return $ret;
	}
	function initialize() {	}
	function execute($action, $form) {
		if ($this->hasPermission($form))
			return $this-> $action ($form);
		else
			return $this->noPermission($form);
	}

	function hasPermission($form){
		$id = $_SESSION[sitename]["id"];
        $permission = $this->permissionNeeded($form);
		if ($permission!=""){
			$role = new Role;
			return $role->userHasPermission($id, $permission);
		} else
			return true;
	}

	function permissionNeeded($form){
		return "";
	}

	function noPermission ($form){ // The user has no permission
		$err= $_SESSION[sitename]["Username"] ." needs ".print_r($this->permissionNeeded($form), TRUE);
		trace($err);
	}
	function callAction(& $action) {
		$controller = & new $action->controller;
		$action_selector = & $action->action_selector;
		return $controller-> $action_selector ($action->params);
	}
}
?>
