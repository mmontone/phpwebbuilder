<?php

require_once dirname(__FILE__) . '/NumField.class.php';
class SuperField extends NumField {
    function &visit(&$obj) {
        return $obj->visitedSuperField($this);
    }
    function updateString() {}

   function check() {
        return TRUE;

    }
}

?>