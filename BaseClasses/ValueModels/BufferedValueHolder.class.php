<?php

class BufferedValueHolder extends ValueModel {
	var $value_model;
	var $buffered_value;

    function BufferedValueHolder(&$value_model) {
    	$this->value_model =& $value_model;
    	$this->flush();
    }

    function commit() {
    	$this->value_model->setValue($this->buffered_value);
    }

    function flush() {
    	$this->buffered_value =& $this->value_model->getValue();
    }

    function setPrimitiveValue(&$value) {
    	$this->buffered_value =& $value;
    }

    function &getValue() {
    	return $this->buffered_value;
    }
}

?>