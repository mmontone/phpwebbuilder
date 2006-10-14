<?php

class SelectMultipleHTMLHandler extends SelectHTMLHandler{
    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('multiple', 'multiple');
	}
	function prepareToRender(){
		WidgetHTMLHandler::prepareToRender();
	}
	function valueChanged(&$value_model, &$params) {
		$elements =& $params->component->elements();
		foreach (array_keys($this->opts) as $opt) {
			$this->opts[$opt]->removeAttribute('selected');
		}
		foreach(array_keys($elements) as $e) {
			$element =& $elements[$e];
			$opt = $this->component->options->indexOf($element);
			$this->opts[$opt]->setAttribute('selected', 'selected');
		}
		$this->redraw();
	}

}
?>