<?php

require_once dirname(__FILE__) . '/../newcontroller/PWBObject.class.php';

class FlowView extends PWBObject
{
    var $model;

    function render_on(&$out) {
    	$this->subclassResponsibility();
    }

    function renderWith(&$config) {
      $this->subclassResponsibility();
    }
}

?>