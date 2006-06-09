<?php

class Number extends ValueObject {
    function Number($value) {
    	parent::ValueObject($value);
    }

    function &plus(&$number) {
    	return new Number($this->value + $number->value);
    }
}
?>