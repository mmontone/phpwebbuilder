<?php

class SelectMultipleHTMLHandler extends WidgetHTMLHandler{
    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('multiple', 'multiple');
		//$view->setAttribute('size', (string) $this->getSize());
	}

	function initializeView(&$v){
		$this->appendOptions($v);
	}
	function valueChanged(&$value_model, &$params) {
		$elements =& $params->component->elements();

		foreach (array_keys($this->component->opts) as $opt) {
			$this->component->opts[$opt]->removeAttribute('selected');
		}

		foreach(array_keys($elements) as $e) {
			$element =& $elements[$e];
			$opt = $this->component->options->indexOf($element);
			$this->component->opts[$opt]->setAttribute('selected', 'selected');
		}

		$this->redraw();
	}

}
?>