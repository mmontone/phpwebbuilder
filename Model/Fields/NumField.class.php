<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class NumField extends DataField {
    function NumField ($name, $isIndex) {
        parent::Datafield($name, $isIndex);
    }
    function &visit(&$obj) {
        return $obj->visitedNumField($this);
    }
    function getValue() {
        return str_replace(",",".",$this->value);
    }
    function SQLvalue() {
        return $this->getValue(). ", " ;

    }

    function check() {
        return ereg ("[0-9]+(\.[0-9]*)?", $this->getValue());

    }
}
?>