<?php

require_once 'ViewerFactory.class.php';

class BoolFieldViewerFactory extends ViewerFactory {

    function &componentForField(&$field) {
    	$cb =& new CheckBox(new ValueHolder($field->value));
    	$cb->disable();
    	return $cb;
    }
}
?>