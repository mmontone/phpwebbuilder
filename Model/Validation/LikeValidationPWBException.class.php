<?php

class LikeValidationPWBException extends ValidationPWBException {
	var $value1;
	var $value2;

    function LikeValidationPWBException(&$value1, &$value2) {
    	parent::ValidationPWBException('Values are not alike');
    	$this->value1 =& $value1;
    	$this->value2 =& $value2;
    }
}
?>