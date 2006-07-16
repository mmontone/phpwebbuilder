<?php

require_once dirname(__FILE__) . '/Text.class.php';

class Label extends Text
{
    function Label($string) {
    	parent::Text(new ValueHolder($string));
    }

    function valueChanged(&$value_model, &$params) {
		echo 'Label value changed: ' . $params;
		if ($this->view){
			$this->view->setAttribute('value', $this->printValue());
		}
	}

	function setValue($value) {
		echo 'Setting label value';
		parent::setValue($value);
	}
}

?>