<?php

require_once dirname(__FILE__) . '/ValueModel.class.php';

class AspectAdaptor extends ValueModel
{
	var $get_selector;
	var $set_selector;
	var $subject;

    function AspectAdaptor(&$subject, $aspect) {
    	parent::ValueModel();

    	$this->subject =& $subject;

    	if (is_array($aspect)) {
    		$this->get_selector = $aspect['get'];
    		$this->set_selector = $aspect['set'];
    	}
    	else {
			$this->get_selector = 'get' . $aspect;
			$this->set_selector = 'set' . $aspect;
    	}
    }

    function primitiveSetValue(&$value) {
    	$set_selector = $this->set_selector;
    	$this->subject->$set_selector($value);
    }

    function &getValue() {
    	$get_selector = $this->get_selector;
    	return $this->subject->$get_selector;
    }
}
?>