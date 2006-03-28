<?php

require_once dirname(__FILE__) . '/PWBObject.class.php';

class Translator extends PWBObject
{
    function Translator() {
        $this->dictionary = $this->dictionary();
    }

    function translate($msg) {
        if (!array_key_exists($msg_id, $this->dictionary)) {
            return $msg;
        }

        return $this->dictionary[$msg_id];
    }

    function dictionary() {
    	$this->subclassResponsibility();
    }
}
?>