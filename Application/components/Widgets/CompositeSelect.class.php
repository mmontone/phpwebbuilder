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

        // FIX: I am assuming that the collection is in $this->navigator->objects !!!
        // Possible Solution: receive the collection in the constructor
        $objs =& $this->navigator->objects;
        $objs->onChangeSend('collectionChanged', $this);
        //

        $this->addComponent($this->navigator, 'nav');
    }

    function elementSelected(&$element) {
        $this->value_model->setValue($element);
    }

    function collectionChanged() {
    	//$this->value_model->setValue($n = null);
    }
}

class CompositeMultipleSelect extends CompositeSelect {
	function elementSelected(&$element) {
        $col =& $this->value_model->getValue();
        $col->setEmpty();
        $col->add($element);
    }

    function collectionChanged() {
    	$col =& $this->value_model->getValue();
        $col->setEmpty();
    }
}

?>