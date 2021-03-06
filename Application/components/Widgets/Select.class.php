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
    	} else if ($collection->hasObjects()){
    		$this->displayF =& new FunctionObject($this, 'printObject');
    	} else {
    		$this->displayF =& new FunctionObject($this, 'printPrimitive');
    	}
		$this->initializeOptions();
    }
    function initializeOptions(){
    	//$this->options->onChangeSend('refresh', $this);
        $this->options->addInterestIn('refresh', new FunctionObject($this, 'refresh'), array('execute once' => true));
        $this->options->addInterestIn('changed', new FunctionObject($this, 'refresh'), array('execute once' => true));
        if (($this->getValueIndex() == -1) and (!$this->options->isEmpty())) {
    		$this->setValueIndex($i = 0);
    	}
    }

    function displayElement(&$e){
		$f =& $this->displayF;
		return $f->callWith($e);
	}

	function &printObject(&$object) {
        if ($object!=null){
            $str = $object->printString();
            return $str;
        } else {
            $n="";
            return $n;
        }
	}

	function &printPrimitive(&$primitive) {
		return $primitive;
	}
	function refresh(){
		//$this->options->refresh();
		$this->refreshView();
	}

    function refreshView() {
    	if ($this->viewHandler){
    		$this->viewHandler->updateFromCollection();
    	}
    }

    function viewUpdated($new_value) {
		$value = $this->getValueIndex();
		if ($new_value != $value) {
			$this->viewHandler->pauseRegistering();
			$this->setValueIndex($new_value);
		}
	}
	function setSize($size) {
		$this->size = $size;
	}

	function getSize() {
		return $this->size;
	}
	function getValueIndex() {
		$val =& $this->getValue();
		return $this->options->indexOf($val);
	}

	function setValueIndex(&$index){
		$this->setValue($this->options->at($index));
	}
}

?>