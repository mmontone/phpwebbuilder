<?php

class Select extends FormComponent {
	var $options;
    function Select(&$value_model, &$options) {
    	parent::FormComponent($value_model);
    	$this->options =& $options;
    }

    function initializeView(&$view){
		$view->setTagName('select');
		$this->appendOptions($view);
	}

	function appendOptions(&$view) {
		foreach ($this->options as $text => $value) {
			$option =& new XMLNodeModificationsTracker('option');
			$option->set_attribute('value', $value);
			$option->appendChild(new XMLTextNode($text));
			$view->appendChild($option);
		}
	}

	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('selectedIndex', $this->printValue());
		}
	}
	function prepareToRender(){
		$this->view->setAttribute('selectedIndex', $this->printValue());
	}
}
?>