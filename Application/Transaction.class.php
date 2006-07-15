<?php

class Transaction {
	var $app;
	var $i = 0;
	var $changes;

    function Transaction(&$app) {
    	$this->app =& $app;
    }

    function begin() {
		$this->i++;
    }

    function commit() {
    	$this->i--;
    	if ($this->i == 0)
    		$this->commitChanges();
    }

    function rollback() {
    	$this->i--;
    	if ($this->i == 0)
    		$this->flushChanges();
    }
}
?>