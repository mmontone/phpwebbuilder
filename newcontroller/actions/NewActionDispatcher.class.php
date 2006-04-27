<?php
 
class NewActionDispatcher
{
	function &application() {
		return Application::instance();
	}

	function invalid_action(&$action) {
		$app =& $this->application();
		$app->report_error('invalid_action', array('action' => $action));
	}

	function component_not_found(&$component) {
		$app =& $this->application();
		$app->report_error('component_not_found',array('component' => $component));
	}

	function call_action(&$action) {
		$component =& $action->component;
		if (!$component->call_action($action->action_selector, $action->params))
			$this->invalid_action($action);
		$app =& $this->application();
		$app->notify_changes();
	}

	function &access_component() {
		$app =& $this->application();
        //var_dump($app);
        $component =& $app->component;
		$comp_nesting = 1;

		while (($accessor = $_REQUEST['comp_'. $comp_nesting++]) != null) {
			if ($component->__children[$accessor] == null) {
				$this->component_not_found($component);
			}
			else {
				$component =& $component->__children[$accessor]->component;
			}
		}
		return $component;
	}

	function read_action_selector() {
		if ($_REQUEST['action'] != null) {
			return $_REQUEST['action'];
		}
		else {
			$form_action_array = array_filter(array_keys($_REQUEST), array($this, 'is_form_action'));
			if (empty($form_action_array)) {
				return 'no_action';
			}
			else {
				$action = reset($form_action_array);
				$action = preg_replace('/^action_/','',$action);
				return $action;
			}
		}
	}

	function is_form_action($param) {
		return preg_match('/^action_/', $param);
	}

	function &resolve_application_from_request() {
		$app =& $this->application();
		return $app->backbutton_manager->resolve_application_from_request();
	}

	function dispatch() {
		$form = $_REQUEST;
		unset($form["PHPSESSID"]);
		unset($form["ControllerSubmit"]);
		$delayed=array();
		$elems = array();
		foreach ($form as $dir=>$param){
			$temp = array();
			$c =& $this->getComponent($dir); 
			if($c) {
				$temp[]=& $c;
				$temp[]= $param;
				$temp[]= $dir;
				if ($param=="execute"){
					$delayed[]=$temp;
				} else {
					$elems[]=$temp;
				}
			}
		}
		$ks = array_keys($elems);
		foreach ($ks as $k){
			$elems[$k][0]->viewUpdated($elems[$k][1]);
		}
		$ks = array_keys($delayed);
		foreach ($ks as $k){
			$delayed[$k][0]->viewUpdated($delayed[$k][1]);
		}
	}
	function &getComponent($path){		
		$app =& Application::instance();
		$comp=& $app->component;
		$path = split("/", $path);
		if ($path[0]=="app"){
			array_shift($path);
			array_shift($path);
			foreach($path as $p){
				$comp1 =& $comp->component_at($p);
				$comp =& $comp1;
			}
			return $comp; 
		} else {
			$n = null;
			return $n;
		}
	}
}

?>