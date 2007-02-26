<?php

class Select extends Widget {
	var $options;
	var $displayF;
	var $selected_index = -1;
	var $size = 1;

    function Select(&$value_model, &$collection, $displayF=null) {
    	#@typecheck $collection:Collection@#
    	parent::Widget($value_model);

    	$this->options =& $collection;

    	if ($displayF!=null){
    		$this->displayF=$displayF;
    	} else if (is_object($collection->first())){
    		$this->displayF =& new FunctionObject($this, 'printObject');
    	} else {
    		$this->displayF =& new FunctionObject($this, 'printPrimitive');
    	}
		$this->initializeOptions();
    }
    function initializeOptions(){
    	$this->options->onChangeSend('refresh', $this);
        if (($this->getValueIndex() == -1) and (!$this->options->isEmpty())) {
    		$this->setValueIndex($i = 0);
    	}
    }

    function displayElement(&$e){
		$f =& $this->displayF;
		return $f->callWith($e);
	}

	function &printObject(&$object) {
		$str = $object->printString();
		return $str;
	}

	function &printPrimitive(&$primitive) {
		return $primitive;
	}
	function refresh(){
		$this->options->refresh();
		$this->refreshView();
	}

    function refreshView() {
    	$this->viewHandler->updateFromCollection();
    }

    function viewUpdated($new_value) {
		$value = $this->getValueIndex();
		if ($new_value != $value)
			$this->setValueIndex($new_value);
	}
	function setSize($size) {
		$this->size = $size;
	}

	function getSize() {
		return $this->size;
	}
	function getValueIndex() {
		return $this->options->indexOf($this->getValue());
	}

	function setValueIndex(&$index){
		$this->setValue($this->options->at($index));
	}
}

?>