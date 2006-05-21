<?
//require_once dirname(__FILE__).'/../newcontroller/Component.class.php';

class Controller //extends Component
{
	var $form;
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
	function setForm($form) {
		$this->form = $form;
	}
	function getForm() {
		if ($this->form == NULL) {
			return $_REQUEST;
		}
		else {
			$temp = $this->form;
			$this->form = NULL;
			return $temp;
		}
	}
	function initialize() {
		//$this->addComponent(new Text(new ValueHolder($t="")), "bodyController");
	}
	/*
	function controller_action($form){}
	function controller_display($form){}
	function begin($form){
		$this->controller_action($form);
		return $this->controller_display($form);
	}
	*/

	// Deprecated
	/*
	function prepareToRender(){
		$form = $this->getForm();
			if (isset ($form["Controller"]) && (strcasecmp(get_class($this),$form["Controller"])!=0)){
				$newcon =& new $form["Controller"];
				$newcon->setForm($form);
				$this->stopAndCall($newcon);
				$newcon->prepareToRender();
			} else {
				$res = $this->begin($form);
			$this->bodyController->setText($res);
		}
		parent::prepareToRender();
	}
	*/

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

	/*
	function declare_actions(){
		return array();
	}
	*/

	function callAction(& $action) {
		$controller = & new $action->controller;
		$action_selector = & $action->action_selector;
		return $controller-> $action_selector ($action->params);
	}
}
?>
