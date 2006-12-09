<?php

class CheckBox extends Widget {
	function valueFromForm(& $params) {
		return $params == '1';
	}

	function setOnChangeEvent() {
		$class = getClass($this);
		$this->events->atPut('onchange', $a=array('onclick',"enqueueChange(getEventTarget(event),checkboxGetValue); " . $this->componentChangeJSFunction() . "(getEventTarget(event))"));
	}
}

?>