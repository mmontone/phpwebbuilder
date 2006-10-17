<?php

class TextAreaComponentXULHandler extends WidgetXULHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('textbox');
		$v->setAttribute('multiline', 'true');
		return $v;
	}
	function prepareToRender() {
		$this->valueChanged($this->component->value_model, $n=null);
	}
	function updateDisabled(&$vh){
		if ($vh->getValue()) {
			$this->view->setAttribute('disabled','true');
		} else {
			$this->view->removeAttribute('disabled');
		}
	}
	function valueChanged(&$value_model, &$params) {
		$text = & $this->component->printValue();
		$this->view->setAttribute('value', $text);
	}

}
?>