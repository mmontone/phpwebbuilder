<?php

class HtmlAreaViewerFactory extends ViewerFactory {

    function &createInstanceFor(&$field) {
    	$ta =& new TextAreaComponent($field);
    	$ta->disable();
    	return $ta;
    }
}
?>