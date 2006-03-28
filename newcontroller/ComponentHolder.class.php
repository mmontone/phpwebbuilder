<?php

require_once dirname(__FILE__) . '/Component.class.php';

/* Should ComponentHolder be a Component decorator ? */
class ComponentHolder
{
	var $component;
	var $__owner_index;

	function ComponentHolder(&$component,$owner_index = null/*,&$owner=null,$owner_index=null*/) {
		$this->hold($component);
	   $this->__owner_index = $owner_index;
	}

	function owner_index() {
		return $this->__owner_index;
	}

    function hold(&$component) {
		$component->holder =& $this;
		$this->component =& $component;
	}

    function &copy_for_backtracking() {
        /* PHP4 */ 
        $my_copy = $this;
        $my_copy->component = $this->component->copy_for_backtracking();
        return $my_copy;
    }
}

/* Component holder as a decorator */
/*
class ComponentReference extends Component
{
	var $component;
	var $root_index;

	function ComponentReference($component,$root_index) {
		$this->component = $component;
		$this->root_index = $root_index;
	}

	function become($component) {
		$component->holder = $this;
		$this->component = $component;
	}

	function app() {
		$component = $this->component;
		return $component->app();
	}

	function render_action($action) {
		$component = $this->component;
		return $component->render_action($action);
	}

	function notify($message, $callback_action) {
		$component = $this->component;
		return $component->notify($message,$callback_action);
	}

	function call($component) {
		$component = $this->component;
		return $component->call($component);

	}

	function callback($callback_key, $parameters=null) {
		$component = $this->component;
		return $component->callback($callback_key,$parameters);
	}
}

*/

?>