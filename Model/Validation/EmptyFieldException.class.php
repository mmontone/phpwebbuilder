<?php

class EmptyFieldException extends ValidationException {
    function accept(&$visitor) {
		return $visitor->visitEmptyFieldException($this);
    }

    function &getField() {
    	return $this->getContent();
    }
}
?>