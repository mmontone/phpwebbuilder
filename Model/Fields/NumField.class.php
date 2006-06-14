<?php

require_once dirname(__FILE__) . '/DataField.class.php';

class NumField extends DataField {
    var $range = null;

    function NumField ($name, $isIndex) {
        parent::Datafield($name, $isIndex);
        if (is_array($isIndex)) {
        	$this->range = $isIndex['range'];
        }
    }

	function isRanged() {
		return $this->range != null;
	}

	function getRange() {
		return $this->range;
	}

    function &visit(&$obj) {
        return $obj->visitedNumField($this);
    }
    function getValue() {
        return str_replace(",",".",parent::getValue());
    }
    function SQLvalue() {
        return $this->getValue(). ", " ;

    }

    function check() {
        return ereg ("[0-9]+(\.[0-9]*)?", $this->getValue());

    }
}
?>