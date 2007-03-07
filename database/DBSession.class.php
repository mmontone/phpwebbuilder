<?php

#@preprocessor Compiler::usesClass(__FILE__, constant('db_driver'));@#

class DBSession {
	var $lastError;
	var $lastSQL = '';
	var $rollback_on_error = true;
	var $rollback = false;
	var $nesting = 0;
	var $commands = array();
	var $registeredObjects = null;
    function registerSave(&$object) {
    	if ($object->existsObject()) {
   			$this->addCommand(new UpdateObjectDBCommand($object));
    	}
    	else {
    		$this->addCommand(new CreateObjectDBCommand($object));
    	}
    }

    function registerDelete(&$object) {
    	$this->addCommand(new DeleteObjectDBCommand($object));
    }

    function addCommand(&$command) {
        #@sql_echo echo 'Adding command ' . getClass($command) . ' target: ' . getClass($command->object) . '<br/>';@#
    	$this->commands[] =& $command;
    }

    function commitTransaction() {
		$this->driver->commit();

		foreach (array_keys($this->commands) as $c) {
			$cmd =& $this->commands[$c];
			$cmd->commit();
		}
		$this->rollback = false;
		$this->commands = array();

		$this->registeredObjects = null;
    }
	function registerObject(&$object){
		if ($this->registeredObjects!==null){
			#@sql_echo echo('registering '.$object->printString());@#
			$set = isset($this->registeredObjects[$object->getInstanceId()]);
			$this->registeredObjects[$object->getInstanceId()]=&$object;
			$object->toPersist = true;
			if (!$set && !$object->existsObject){
				$object->registerCollaborators();
			}
		}
	}
	function &beginRegisteringAndTransaction(){

		$db =& DBSession::beginRegistering();
		$db->beginTransaction();
		return $db;
	}
	function &beginRegistering(){
		$db =& DBSession::Instance();
		if ($db->registeredObjects==null)$db->registeredObjects=array();
		return $db;
	}
	function flushChanges(){
		foreach(array_keys($this->registeredObjects) as $k){
			$this->registeredObjects[$k]->flushChanges();
		}
		$this->registeredObjects=null;
	}
    function rollbackTransaction() {
		$this->driver->rollback();

		foreach (array_keys($this->commands) as $c) {
			$cmd =& $this->commands[$c];
			$cmd->rollback();
		}
		$this->rollback = false;
		$this->commands = array();
    }

	function beginTransaction() {
		$this->nesting++;

		if ($this->nesting == 1) {
			$this->driver->beginTransaction();
		}

		#@sql_echo print_backtrace('Beggining transaction ('. $this->nesting . ')');@#
	}

	function &commit() {
		$db =& DBSession::Instance();
		return $db->doCommit();
	}
	function &doCommit(){
		#@gencheck if ($this->nesting <= 0)
        {
		  print_backtrace('Error: trying to commit a non existing transaction');
		}//@#

        if ($this->nesting == 1) {
			if ($this->registeredObjects!==null){
				while(!empty($this->registeredObjects)){
					$ks = array_keys($this->registeredObjects);
					$elem =& $this->registeredObjects[$ks[0]];
					unset($this->registeredObjects[$ks[0]]);
					$this->save($elem);
				}
			}
			if (!$this->rollback) {
				#@sql_echo print_backtrace( 'Commiting transaction ('. $this->nesting . ')<br/>');@#
				$this->commitTransaction();
			}
			else {
				#@sql_echo	print_backtrace('Rollback transaction ('. $this->nesting . ')<br/>');@#
				$this->rollbackTransaction();
			}
		}
		#@sql_echo
		else {
			if (!$this->rollback) {
				print_backtrace( 'Commiting transaction ('. $this->nesting . ')<br/>');
			}
			else {
				print_backtrace('Rollback transaction ('. $this->nesting . ')<br/>');
			}
		}//@#

		$this->nesting--;
		$n=null;
		return $n;
	}

	function rollback() {
		#@gencheck if ($this->nesting <= 0)
        {
          print_backtrace('Error: trying to rollback a non existing transaction');
        }//@#

        #@sql_echo print_backtrace( 'Rolling back transaction ('. $this->nesting . ')<br/>');@#

		if ($this->nesting == 1) {
			$this->rollbackTransaction();
		}
		else {
			#@sql_echo print_backtrace('Setting rollback in true <br/>');@#

			$this->rollback=true;
		}

		$this->nesting--;
	}

	function &Instance(){
		$slot = 'db_session';
		if (!Session::isSetAttribute($slot)){
			$dbsession_class = 'DBSession';
			if (defined('dbsession_class')) {
				$dbsession_class = constant('dbsession_class');
			}
			$driver_class = constant('db_driver');
			$dbsession =& new $dbsession_class;
			$dbsession->driver =& new $driver_class($dbsession);
			Session::setAttribute($slot,$dbsession);
		}
		return Session::getAttribute($slot);
	}

	function lastError(){
		$last_error =& DBSession::GetLastError();
		return $last_error->getMessage();
	}

	function &GetLastError() {
		$db =& DBSession::instance();
		return $db->lastError;
	}

	function lastSQL(){
		$db =& DBSession::instance();
		return $db->lastSQL;
	}

	function &SQLExec($sql, $getID, &$obj, &$rows) {
       	return $this->driver->SQLExec($sql, $getID, $obj, $rows);
    }

    function queryDB($query){
    	return $this->driver->queryDB($query);
    }

    function setLastError(&$error) {
		$this->lastError =& $error;
	}

	function &getLastErr() {
		return $this->lastError;
	}

	function clearLastSQL() {
		$this->setLastSQL('');
		if ($this->lastError) {
			$this->lastError->sql='';
			$this->lastError->backtrace='';
		}
	}

	function setLastSQL($sql) {
		$this->lastSQL = $sql;
	}

	function getLastSQL() {
		return $this->lastSQL;
	}

	function fetchrecord($res) {
    	return $this->driver->fetchrecord($res);
    }

    function openDatabase() {
    	return $this->driver->openDatabase();
    }
    function closeDatabase() {
      return $this->driver->closeDatabase();
    }

    function fetchArray($res) {
		return $this->driver->fetchArray($res);
	}

	function query($q) {
		return $this->driver->query($q);
	}

	function batchExec($sqls) {
		return $this->driver->batchExec($sqls);
	}

	function &save(&$object) {
		$db=&DBSession::Instance();
		$db->registerSave($object);
		$res =& $object->save();
		if (is_exception($res)) {
			if ($db->rollback_on_error) {
				$db->rollback();
			}
		}

		return $res;
	}

	function &delete(&$object) {
		$this->registerDelete($object);

		$res =& $object->delete();
		if (is_exception($res)) {
			if ($this->rollback_on_error) {
				$this->rollback();
			}
		}
		return $res;
	}

	function rollbackOnError($b = true) {
		$this->rollback_on_error = $b;
	}
}


class DBCommand {
	var $object;

	function DBCommand(&$object) {
		$this->object =& $object;
	}

	function commit() {
		print_backtrace_and_exit('Subclass responsibility');
	}

	function rollback() {
		print_backtrace_and_exit('Subclass responsibility');
	}

	function &getObject() {
		return $this->object;
	}
}

class CreateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_echo 	echo 'Committing creation: ' . getClass($this->object) . '<br />';@#

		$this->object->commitMetaFields();
	}

	function rollback() {
		#@sql_echo echo 'Rolling back creation: ' . getClass($this->object) . '<br />';@#

		$this->object->flushInsert();
	}
}

class UpdateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_echo echo 'Committing update: ' . getClass($this->object) . '<br />';@#

		$this->object->commitMetaFields();
	}

	function rollback() {
		#@sql_echo echo 'Rolling back update: ' . getClass($this->object) . '<br />';@#

		$this->object->flushUpdate();
	}
}

class DeleteObjectDBCommand extends DBCommand {
	function commit() {

	}

	function rollback() {
		#@sql_echo  echo 'Rolling back delete: ' . getClass($this->object) . '<br />';@#

		$this->object->existsObject=TRUE;
	}
}

?>