<?php

class InputHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender(){
		$this->valueChanged($this->component->value_model, $n=null);
	}
	function initializeDefaultView(&$view){
		$view->setTagName('input');
		$view->setAttribute('type', 'text');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$app =& Application::instance();
			$this->view->setAttribute('value', $app->toAjax($this->component->printValue()));
		}
	}
}

?>