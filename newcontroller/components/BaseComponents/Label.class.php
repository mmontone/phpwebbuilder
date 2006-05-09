<?php

class Label extends Text{

    function Label($string) {
    	parent::Text(new ValueHolder($string));
    }
}
?>