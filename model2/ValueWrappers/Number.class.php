<?php

class Number extends ValueWrapper {
    function Number($value) {
    	parent::ValueWrapper($value);
    }

    function &plus(&$number) {
    	return new Number($this->value + $number->value);
    }
}
?>