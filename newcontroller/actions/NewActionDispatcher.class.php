<?php

class NewActionDispatcher
{
	function is_action_parameter($request_parameter) {
		return preg_match('/^p_/' , $request_parameter) ;
	}

	function collect_params() {
		$param_keys = array_keys($_REQUEST);
		$param_keys = array_filter($param_keys, array($this, 'is_action_parameter'));
		$params = array();
		foreach ($param_keys as $param_key) {
			$new_param_key = preg_replace('/^p_/',  '', $param_key);
			$params[$new_param_key] = $_REQUEST[$param_key];
		}
		return $params;
	}

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
		foreach ($form as $dir=>$param){
			if ($param=="execute"){
				$delayed[$dir]=$param;
			} else {
				$this->dispatchTo($dir, $param);
			}
		}
		foreach ($delayed as $dir=>$param){
			$this->dispatchTo($dir, $param);
		}
		
		
	}
	function dispatchTo($path, $param){
		$app =& Application::instance();
		$comp=& $app->component;
		$path = split("/", $path);
		array_shift($path);
		foreach($path as $p){
			if ($comp ==null) echo $p;
			$comp =& $comp->component_at($p);
		}
		if (!$comp) backtrace();
		$comp->viewUpdated($param);
	}
}

?>