<?php

class CompositeSelect extends Component {
	var $value_model;
	var $navigator;

    function CompositeSelect(&$value_model, &$navigator) {
		#@typecheck $value_model:ValueModel, $navigator:CollectionNavigator#@
		$this->value_model =& $value_model;
		$this->navigator =& $navigator;
        parent::Component();
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