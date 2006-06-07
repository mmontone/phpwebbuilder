<?php

require_once dirname(__FILE__) . '/TextField.class.php';

class HtmlArea extends TextArea
{
    function HtmlArea ($name, $isIndex) {
               parent::TextArea($name, $isIndex);
    }

    function &visit(&$obj) {
        return $obj->visitedHtmlArea($this);
    }
}

?>