<?php

require_once dirname(__FILE__) . '/../Application/PWBObject.class.php';

class Model extends PWBObject
{
    var $controller;
    var $view;

    function Model() {
    	parent::PWBObject();
    }
}

?>