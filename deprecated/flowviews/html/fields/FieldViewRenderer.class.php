<?php

class FieldViewRenderer extends PWBObject
{
    function FieldViewRenderer() {

    }

    function render(&$field, &$out) {
    	$view =& $this->viewFor($field);
        $view->render(&$out);
    }

    function viewFor(&$field) {
    	$this->subclassResponsibility();
    }
}

?>