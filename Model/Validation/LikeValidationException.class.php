<?php

class LikeValidationException extends ValidationException {
	var $value1;
	var $value2;

    function LikeValidationPWBException(&$value1, &$value2) {
    	parent::ValidationException('Values are not alike');
    	$this->value1 =& $value1;
    	$this->value2 =& $value2;
    }
}
?>