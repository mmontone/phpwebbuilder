<?php

class CheckBoxHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender() {
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
	}
	function initializeDefaultView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'checkbox');
	}
	function valueChanged(& $value_model, & $params) {
			if ($this->component->getValue()) {
				$this->view->setAttribute('checked', 'checked');
			} else {
				$this->view->removeAttribute('checked');
			}
	}
}
?>