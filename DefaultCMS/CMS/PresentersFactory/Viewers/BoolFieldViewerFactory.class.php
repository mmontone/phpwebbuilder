<?php

require_once 'ViewerFactory.class.php';

class BoolFieldViewerFactory extends ViewerFactory {

    function &createInstanceFor(&$field) {
    	$cb =& new CheckBox($field);
    	$cb->disable();
    	return $cb;
    }
}
?>