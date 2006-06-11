<?php

class Number extends ValueObject {
    function &plus(&$number) {
    	return new Number($this->value + $number->value);
    }
}
?>