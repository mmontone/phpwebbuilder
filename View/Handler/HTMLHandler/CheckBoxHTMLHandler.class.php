<?php

class CheckBoxHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender() {
		parent::prepareToRender();
		if ($this->component->getValue())
			$this->view->setAttribute('checked', 'checked');
	}
	function initializeDefaultView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'checkbox');
	}
}
?>