<?php

class OneOfException extends ValidationException {
	function &getFields() {
    	return $this->getContent();
    }

    function accept(&$visitor) {
    	return $visitor->visitOneOfException($this);
    }
}
?>