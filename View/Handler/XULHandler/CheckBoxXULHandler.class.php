<?php

class CheckBoxXULHandler extends WidgetXULHandler{
/* <checkbox id="case-sensitive" checked="true" label=""/> */
	function prepareToRender() {
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
	}
	function initializeDefaultView(&$view) {
		$view->setTagName('checkbox');
	}
	function valueChanged(& $value_model, & $params) {
			if ($this->component->getValue()) {
				$this->view->setAttribute('checked', 'true');
			} else {
				$this->view->removeAttribute('checked');
			}
	}
}
?>