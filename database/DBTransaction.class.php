<?php

class DBTransaction {
	var $commands;
	var $driver;

    function DBTransaction(&$driver) {
    	$this->commands = array();
    	$this->driver =& $driver;
    }

    function save(&$object) {
    	if ($object->existsObject()) {
   			$this->addCommand(new UpdateObjectDBCommand($object));
    	}
    	else {
    		$this->addCommand(new CreateObjectDBCommand($object));
    	}
    }

    function delete(&$object) {
    	$this->addCommand(new DeleteObjectDBCommand($object));
    }

    function addCommand(&$command) {
    	$this->commands[] =& $command;
    }

    function begin() {
		$this->driver->beginTransaction();
    }

    function commit() {
		foreach (array_keys($this->commands) as $c) {
			$cmd =& $this->commands[$c];
			$cmd->commit();
		}

		$this->driver->commit();
    }

    function rollback() {
		foreach (array_keys($this->commands) as $c) {
			$cmd =& $this->commands[$c];
			$cmd->rollback();
		}

		$this->driver->rollback();
    }
}

class DBCommand {
	var $object;

	function DBCommand(&$object) {
		$this->object =& $object;
	}

	function commit() {
		print_backtrace('Subclass responsibility');
	}

	function rollback() {
		print_backtrace('Subclass responsibility');
	}

	function &getObject() {
		return $this->object;
	}
}

class CreateObjectDBCommand extends DBCommand {
	function commit() {

	}

	function rollback() {
		$this->object->id->setValue($n = null);
		$this->object->existsObject = false;
	}
}

class UpdateObjectDBCommand extends DBCommand {
	function commit() {

	}

	function rollback() {

	}
}

class DeleteObjectDBCommand extends DBCommand {
	function commit() {

	}

	function delete() {

	}
}

?>