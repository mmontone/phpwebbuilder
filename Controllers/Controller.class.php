<?

require_once dirname(__FILE__).'/../newcontroller/Component.class.php';

class Controller extends Component
{
   	var $form;
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
	function setForm($form){
		$this->form = $form;
	}
	function getForm(){
		if ($this->form == NULL){
			return $_REQUEST;
		} else {
			$temp = $this->form; 
			$this->form = NULL;
			return $temp;
		}
	}
	function Controller(){
		parent::Component();	
    	$this->add_component(new Text(""), "bodyController");
    }
	function controller_action($form){}
	function controller_display($form){}
	function begin($form){
		$this->controller_action($form);
		return $this->controller_display($form);
	}
	function prepareToRender(){
 		if (isset ($_REQUEST["Controller"]) && (strcasecmp(get_class($this),$_REQUEST["Controller"])!=0)){
 			$this->stopAndCall(new $_REQUEST["Controller"]);
 			$this->holder->component->renderContent($html);
 		} else {
 			$form = $this->getForm();
			$t =& $this->component_at("bodyController");
			$t->setText($this->execute("begin",$form));
 		}
	}
	function execute ($action,$form) {
          if ($this->hasPermission($form))
            return $this->$action($form);
          else
            return $this->noPermission($form);
        }

	function declare_actions(){
		return array();
	}
    function callAction(&$action) {
      $controller =& new $action->controller;
      $action_selector = $action->action_selector;
      return $controller->$action_selector($action->params);
    }

}
?>
