<?php

class AspectAdaptor extends ObjectHolder
{
	var $get_selector;
	var $set_selector;

    function AspectAdaptor(&$object, $aspect) {
    	parent::ObjectHolder($object);

    	if (is_array($aspect)) {
    		$this->get_selector = $aspect['get'];
    		$this->set_selector = $aspect['set'];
    	}
    	else {
			$this->get_selector = 'get' . ucfirst($aspect);
			$this->set_selector = 'set' . ucfirst($aspect);
    	}
    }

    function primitiveSetValue(&$value) {
    	$set_selector = $this->set_selector;
    	$this->__value->$set_selector($value);
    }

    function &getValue() {
    	$get_selector = $this->get_selector;
    	return $this->__value->$get_selector();
    }
}
?>