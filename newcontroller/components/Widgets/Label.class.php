<?php

require_once dirname(__FILE__) . '/Text.class.php';

class Label extends Text
{
    function Label($string) {
    	parent::Text(new ValueHolder($string));
    }
}
?>