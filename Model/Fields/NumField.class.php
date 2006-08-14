<?php

class NumField extends DataField {
    var $range = null;
	function createInstance($params){
		parent::createInstance($params);
		$this->range = $params['range'];
	}
	function defaultValues($params){
		return array_merge(array('range'=>null, 'numtype'=>'int'), parent::defaultValues($params));
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

    function validate() {
        $ok =& ereg ("[0-9]+(\.[0-9]*)?", $this->getValue());
        if (!$ok) {
			$ex =& new ValidationException(array('message' => $this->displayString . ' is not a number', 'content' => & $this));
			$this->triggerEvent('invalid', $ex);
			return $ex;
        }

        $this->triggerEvent('validated', $this);
        return false;
    }
}
?>