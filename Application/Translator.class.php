<?php

class Translator extends PWBObject
{
    var $dictionary;

    function Translator() {
        $this->dictionary = $this->dictionary();
    }

    function translate($msg) {
        if (!array_key_exists($msg, $this->dictionary)) {
            return $msg;
        }

        return $this->dictionary[$msg];
    }

    function dictionary() {
    	$this->subclassResponsibility();
    }
}
?>