<?php

class ValidationException extends PWBException {
	function accept(&$visitor) {
		$visitor->visitValidationException($this);
	}
}

?>