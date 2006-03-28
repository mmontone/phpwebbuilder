<?php

class Action
{
	var $component;
	var $action_selector;
	var $params;

	function Action(&$component, $action_selector, &$params) {
		$this->component =& $component;
		$this->action_selector = $action_selector;
		$this->params =& $params;
	}
}

?>