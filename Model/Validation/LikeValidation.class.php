<?php

class LikeValidation extends Validation {
	var $likeValue;

    function LikeValidation(&$value_model, &$likeValue) {
    	parent::Validation($value_model);
    	$this->likeValue =& $likeValue;
    }

    function validate(&$error_handler) {
    	if ($this->getValue() != $this->likeValue) {
    		$e =& new LikeValidationPWBException($this->getValue(), $this->likeValue);
    		$e->raise($error_handler);
    	}
    }

    function &likeValue() {
    	return $this->like_value;
    }
}
?>