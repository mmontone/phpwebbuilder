<?php

require_once dirname(__FILE__) . '/../Application/PWBObject.class.php';

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