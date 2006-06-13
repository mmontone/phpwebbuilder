<?php

require_once 'ViewerFactory.class.php';

class BoolFieldViewerFactory extends ViewerFactory {

    function &componentForField(&$field) {
    	$cb =& new CheckBox(new AspectAdaptor($field, 'Value'));
    	$cb->disable();
    	return $cb;
    }
}
?>