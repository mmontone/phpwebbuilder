<?php

class DBError extends PWBException {
	var $number;
	var $sql;
    var $target;

	function createInstance($params) {
		$this->number = @$params['number'];
		$this->sql = @$params['sql'];
        $this->target =& $params['target'];
		parent::createInstance($params);
	}

    function setTargetObject(&$target) {
    	$this->target =& $target;
    }

	function getNumber() {
		return $this->number;
	}

	function getSQL() {
		return $this->sql;
	}

    function printString() {
    	$this->primPrintString('message: ' . $this->getMessage() . ' error: ' . $this->getNumber() . ' sql: ' . $this->getSQL());
    }

	function printHtml() {
		return 'DBError: <br/>Number: ' . $this->getNumber() . '<br />Message: ' . $this->getMessage() . '<br />SQL: ' . $this->getSQL();
	}

    function &raise() {
        if (is_object($this->target)) {
        	return $this->target->raiseDBError($this);
        }
        else {
        	return parent::raise();
        }
    }
}


?>