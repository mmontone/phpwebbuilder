<?php

class DBError extends PWBException {
	var $number;
	var $sql;

	function createInstance($params) {
		$this->number = $params['number'];
		$this->sql = $params['sql'];
		parent::createInstance($params);
	}

	function getNumber() {
		return $this->number;
	}

	function getSQL() {
		return $this->sql;
	}

	function printHtml() {
		return 'DBError: <br/>Number: ' . $this->getNumber() . '<br />Message: ' . $this->getMessage() . '<br />SQL: ' . $this->getSQL();
	}
}

?>