<?php

require_once dirname(__FILE__) . '/TextField.class.php';

class HtmlArea extends TextArea
{
    function &visit(&$obj) {
        return $obj->visitedHtmlArea($this);
    }
}

?>