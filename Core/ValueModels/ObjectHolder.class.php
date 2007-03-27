<?php

class ObjectHolder extends PWBObject {#@use_mixin ValueModel@#
	var $modelSendsUpdates = false;
    var $__value;

    function ObjectHolder(&$value) {
    	parent::PWBObject();
    	$this->__value =& $value;
    }

    function &getValue() {
    	return $this->__value;
    }

	function setValue(& $value) {
		$old_value =& $this->getValue();
		$this->primitiveSetValue($value);
		if (!$this->modelSendsUpdates) {
			$this->triggerEvent('changed', $this->__value);
		}
	}

    function primitiveSetValue(&$value) {
    	$this->__value =& $value;
    }

    function setModelSendsUpdates($bool) {
    	$this->modelSendsUpdates =& $bool;

    	if ($bool) {
    		$this->__value->addInterestIn('changed', new FunctionObject($this, 'modelChanged'));
    	}
    	else {
    		$this->release();
    	}
    }

    function modelChanged() {
    	$this->triggerEvent('changed', $this->getValue());
    }

    function printString() {
    	if (is_null($this->__value)) {
            return $this->primPrintString('null');
        }
        else {
        	return $this->primPrintString($this->__value->printString());
        }
    }
}

?>