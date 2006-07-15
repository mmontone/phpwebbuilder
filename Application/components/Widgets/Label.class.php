<?php

require_once dirname(__FILE__) . '/Text.class.php';

class Label extends Text
{
    function Label($string_or_valueholder) {
    	if (is_string($string_or_valueholder)) {
    		parent::Text(new ValueHolder($string_or_valueholder));
    	} else {
    		parent::Text($string_or_valueholder);
    	}

    }
	function setValue($value) {
		//echo 'Setting label value';
		parent::setValue($value);
	}

}
?>