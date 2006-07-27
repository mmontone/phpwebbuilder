<?php

require_once 'ViewerFactory.class.php';

class TextAreaViewerFactory extends ViewerFactory {

    function &createInstanceFor(&$field) {
    	$ta =& new TextAreaComponent($field);
    	$ta->disable();
    	return $ta;
    }
}
?>