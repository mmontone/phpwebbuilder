<?php

class Label extends Text
{
    function Label($string) {
    	parent::Text(new ValueHolder($string));
    }
	//TODO Why is this here?
    function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('value', $this->printValue());
		}
	}
	function setValue($value) {
		parent::setValue($value);
	}
}

?>