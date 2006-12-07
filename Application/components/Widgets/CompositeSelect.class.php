<?php

class CompositeSelect extends Component {
	var $value_model;
	var $navigator;

    function CompositeSelect(&$value_model, &$navigator) {
		$this->value_model =& $value_model;
		$this->navigator =& $navigator;
    }

    function initialize() {
		$this->addComponent(new Text($this->value_model), 'display');
		$this->navigator->registerCallback('element_selected', new FunctionObject($this, 'elementSelected'));
		$this->addComponent($this->navigator, 'nav');
    }

    function elementSelected(&$element) {
    	$this->value_model->setValue($element);
    }
}

?>