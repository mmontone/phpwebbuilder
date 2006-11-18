<?php

class SelectMultipleXULHandler extends SelectMultipleHTMLHandler{
    function initializeDefaultView(&$view){
		$view->setTagName('listbox');
	}
	function prepareToRender(){
		WidgetHTMLHandler::prepareToRender();
	}
	function updateFromCollection() {
		$this->updateViewFromCollection($this->view->first_child());
	}
	function valueChanged(&$value_model, &$params) {
		$elements =& $params->component->elements();
		foreach (array_keys($this->opts) as $opt) {
			$this->opts[$opt]->removeAttribute('selected');
		}
		foreach(array_keys($elements) as $e) {
			$element =& $elements[$e];
			$opt = $this->component->options->indexOf($element);
			$this->opts[$opt]->setAttribute('selected', 'true');
		}
		$this->redraw();
	}
	function appendOptions(&$view) {
		$i=0;
		$self =& $this;
		$this->component->options->map(
			lambda('&$elem',
			'$option =& new XMLNodeModificationsTracker(\'listitem\');
			$option->setAttribute(\'value\', $i);
			$option->setAttribute(\'label\',$self->component->displayElement($elem)));
			$self->opts[$i] =& $option;
			$view->appendChild($option);
			$i++;', get_defined_vars()));
	}

}
?>