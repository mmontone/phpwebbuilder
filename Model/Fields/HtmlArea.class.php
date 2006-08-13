<?php

class HtmlArea extends TextArea
{
    function &visit(&$obj) {
        return $obj->visitedHtmlArea($this);
    }
}

?>