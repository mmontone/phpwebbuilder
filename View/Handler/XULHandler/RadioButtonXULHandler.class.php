<?php
class RadioButtonXULHandler extends WidgetXULHandler{
/*<radio id="orange" label="Orange"/>*/
    function initializeView(&$view){
		$view->setTagName('radio');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->component->value_model->getValue()) {
			$this->view->setAttribute('selected','true');
		} else{
			$this->view->removeAttribute('selected');
		}
	}
	function prepareToRender() {
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
	}
}
?>