<?php

class BackButtonManager
{
	
}

class DontCareBackButtonManager
{
	function &resolve_application_from_request() {
		return Application::instance();
	}
	
	function application_changed() {}

	function append_url_parameters(&$url) {}
	
	function append_form_fields(&$out) {}

}

class BackButtonTracker extends BackButtonManager {
	
}
/* 
I'm losing control between ProhibitBackButtonManager and application_changed.
It would be interesting to try coroutines.
From wikipedia:
In situations in which a coroutine would be the natural implementation of a mechanism,
but is not available, the typical response is to create a subroutine that uses an ad-hoc
assemblage of boolean flags and other state variables to maintain an internal state between
calls. Conditionals within the code result in the execution of different code paths on
successive calls, based on the values of the state variables.
Another typical response is to implement an explicit state machine in the form of a large
and complex switch statement. Such implementations are difficult to understand and maintain.
*/

/* This class is not working well */
class ProhibitBackButtonManager extends BackButtonTracker
{
	var $app_id;
		
	function ProhibitBackButtonManager() {
			$this->app_id = 1;
	 }
	
	function application_changed() {
		$this->app_id++;
	}
	
	function &resolve_application_from_request() {
		$app =& Application::instance();
		$requested_app_id = $_REQUEST['app'];
		
		if ($requested_app_id == null) {
			$app->url_manager->invalid_application();
		}
		else {
			if ($requested_app_id < ($this->app_id - 1)) {
				$app->report_error('paged_expired', array('message' => 'No back button support'));;
			}
		}
			
		$app =& Application::instance();
		return $app;	
	}

	function append_url_parameters(&$url) {
		$url .= '&app=' . $this->app_id;
	}
	
	function append_form_fields(&$out) {
		$out .= "    <input type=hidden name=app value=" . $this->app_id . " />\n";
	}
}

/* WARNING: this class doesnt work at all yet!! */
class BacktrackBackButtonManager extends BackButtonTracker
{
	var $app_id;
	var $app_instances;
		
	function BacktrackBackButtonManager() {
			$app =& Application::instance();
			$this->app_instances = array();
			$this->app_id = 1; /* To be improved */
       	 	$this->app_instances[1] = $app; /* Obtain a ugly copy */
	}
	
	function &application_instance($app_id) {
		return $this->app_instances[$app_id];
	}
	
	function &current_application_instance() {
		return $this->app_instances[$this->app_id];
	}
	
	function application_changed() {
		$application_copy =& $this->copy_for_backtracking();
		$_SESSION['app'][$_SESSION['app_index']] =& $application_copy;
		$application_copy->instance_id = $_SESSION['app_index'];
		$_SESSION['app_index'] += 1; /*Has to be improved, obviously*/
		$_SESSION['app'][$_SESSION['app_index']] =& $this;
		return $this->session_index;
	}
	
	function &resolve_application_from_request() {
		$app_index = $_REQUEST['app'];
		if ($_REQUEST['app'] == null || $_SESSION['app'][$app_index] == null) {
			$app =& Application::instance();
			$app->url_manager->invalid_application();
		}
		else {
			$app =& $_SESSION['app'][$app_index];
			$_SESSION['app']['current_app'] =& $app;
		}
		return $app;	
	}

	function append_url_parameters(&$url) {
		$url .= '&app=' . $app->instance_id;
	}

}

?>