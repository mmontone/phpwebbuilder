<?

require_once dirname(__FILE__).'/../newcontroller/Component.class.php';

class Controller extends Component
{
	var $view;
  	var $model;
	/**
	 * Special var, for get_subclass
	 */
	var $isClassOfPWB = true;
	function myForm ($name, $content) {
		$ret .="<form action=\"Action.php?Controller=".get_class($this)."";
		$ret .=		"\" method=\"POST\" name=\"$name\">" . $content;
		$ret .=		"<input name=\"exec$name\" type=\"submit\" /></form>";
		return $ret;
	}

    function initialize() {
    	/* put initialization code here, in the subclass */
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
		$err= $_SESSION[sitename]["Username"] ." needs ".print_r($this->permissionNeeded ($form), TRUE);
		trace($err);
	}
	function begin($form){
		echo("Class ".get_class($this). "didn't define begin");
		backtrace();
	}
	function execute ($action, $form) {
		  if ($form==NULL) print_backtrace("The form is empty");
          if ($this->hasPermission($form))
            return $this->$action($form);
          else
            return $this->noPermission($form);
        }

	function render_on(&$html) {
 		if (isset ($_REQUEST["Controller"]) && (strcasecmp(get_class($this),$_REQUEST["Controller"])!=0)){
 			$this->stopAndCall(new $_REQUEST["Controller"]);
 			$this->holder->component->renderContent($html);
 		} else {
			$html->text($this->execute("begin",$_REQUEST));
 		}

	}
	function declare_actions(){
		return array();
	}

        function callAction(&$action) {
          $controller =& new $action->controller;
          $action_selector = $action->action_selector;
          return $controller->$action_selector($action->params);
        }

        function loadView($params) {
          $this->aboutToLoadView($params);
          assert($params['view']);
          $this->view = new $params['view']($params);
          $this->view->controller =& $this;
        }

        function aboutToLoadView(&$params) {}
}
?> 
