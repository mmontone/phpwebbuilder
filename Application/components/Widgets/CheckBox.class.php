<?php
class CheckBox extends Widget {
	var $disabled = false;

	function CheckBox(& $boolHolder) {
		parent :: Widget($boolHolder);
	}

	function valueFromForm(& $params) {
		return $params == '1';
	}

	function setOnChangeEvent() {
		$class = getClass($this);
		//$this->events->atPut('onchange', $a=array('onclick',"enqueueChange(getEventTarget(event),checkboxGetValueInversed); " . $this->componentChangeJSFunction() . "(getEventTarget(event))"));
		$this->events->atPut('onchange', $a=array('onclick',"enqueueChange(getEventTarget(event),checkboxGetValue); " . $this->componentChangeJSFunction() . "(getEventTarget(event))"));
	}
}

 ?>