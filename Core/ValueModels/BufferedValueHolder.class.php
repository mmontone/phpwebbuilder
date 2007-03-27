<?php

class BufferedValueHolder extends PWBObject {#@use_mixin ValueModel@#
	var $value_model;
	var $buffered_value;

    function BufferedValueHolder(&$value_model) {
    	$this->value_model =& $value_model;
    	$this->buffered_value =& $value_model->getValue();
   }

    function commitChanges() {
    	$this->value_model->setValue($this->buffered_value);
    	$this->triggerEvent('changed', $this->getValue());
    }

    function flushChanges() {
    	$this->buffered_value =& $this->value_model->getValue();
    	$this->triggerEvent('changed', $this->getValue());
    }

    function setValue(& $value) {
		$old_value =& $this->getValue();
		$this->primitiveSetValue($value);
		$this->triggerEvent('changed', $this->getValue());
	}

    function setPrimitiveValue(&$value) {
    	$this->buffered_value =& $value;
    }

    function &getValue() {
    	return $this->buffered_value;
    }
}

?>