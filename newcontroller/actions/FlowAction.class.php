<?php

require_once dirname(__FILE__) . '/../PWBObject.class.php';

class FlowAction extends PWBObject
{
	var $action_selector;
	var $params;
    var $component;

	function FlowAction(&$component, $action_selector, $params) {
	   $this->component =& $component;
	   $this->action_selector = $action_selector;
	   $this->params =& $params;
	}

    function href() {
        return $this->component->render_action_link($this->action_selector, $this->params);
    }
}

?>