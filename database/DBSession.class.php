<?php

#@preprocessor Compiler::usesClass(__FILE__, constant('db_driver'));@#

class DBSession {
	var $lastError;
	var $lastSQL = '';
	var $rollback_on_error = false;
	var $rollback = false;
	var $nesting = 0;
	var $commands = array();
	var $registeredObjects = array();

    function DBSession() {
        pwb_register_shutdown_function('dbsession', new FunctionObject($this, 'shutdown'));
    }

    function shutdown() {
    	if ($this->nesting !== 0) {
            $this->rollbackTransaction();
            $this->nesting = 0;
            print_backtrace_and_exit('Error: nesting level > 0 (rolling back transaction)');
        }
    }

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

		$n = array();
        $this->registeredObjects =& $n;
    }

	function registerObject(&$object){
		#@persistence_echo echo 'Registering '.$object->printString() . '<br/>';@#
		if ($this->transactionStarted()){
			$this->save($object);
			$set = false;
		} else {
			$set = isset($this->registeredObjects[$object->getInstanceId()]);
			$this->registeredObjects[$object->getInstanceId()] =& $object;
			$object->toPersist = true;
		}
		if (!$set && !$object->existsObject){
			$object->registerCollaborators();
		}
	}

	function &beginRegisteringAndTransaction(){
		$db =& DBSession::beginRegistering();
		$db->beginTransaction();
		return $db;
	}
	function &beginRegistering(){
		$db =& DBSession::Instance();
		return $db;
	}

	function flushChanges(){
			#@persistence_echo echo 'flushing changes';@#
            foreach(array_keys($this->registeredObjects) as $k){
    			$this->registeredObjects[$k]->flushChanges();
				#@persistence_echo echo 'flushing changes of '.$this->registeredObjects[$k]->printString();@#
    		}
    		$n = array();
            $this->registeredObjects =& $n;
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
		@$GLOBALS['transactionnesting']++;

		if (@$GLOBALS['transactionnesting'] == 1) {
			$this->driver->beginTransaction();
		}

		#@sql_echo echo 'Beggining transaction ('. @$GLOBALS['transactionnesting'] . ')';@#
        #@sql_echo2 print_backtrace();@#
	}
	function transactionStarted(){
		return @$GLOBALS['transactionnesting']>0;
	}
	function &commit() {
		$db =& DBSession::Instance();
		return $db->doCommit();
	}
	function &doCommit(){
		#@gencheck if (@$GLOBALS['transactionnesting'] <= 0)
        {
		  print_backtrace('Error: trying to commit a non existing transaction');
		}//@#
		#@persistence_echo echo 'commiting '.@$GLOBALS['transactionnesting'].'<br/>';@#
        if (@$GLOBALS['transactionnesting'] == 1) {
			$this->saveRegisteredObjects();
			if (!$this->rollback) {
				#@sql_echo echo 'Commiting transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>';@#
                #@sql_echo2 print_backtrace();@#
				$this->commitTransaction();
			}
			else {
				#@sql_echo	echo ('Rollback transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>');@#
                #@sql_echo2 print_backtrace();@#
				$this->rollbackTransaction();
			}
		}
		#@sql_echo
		else {
			if (!$this->rollback) {
				echo ('Commiting transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>');
                #@sql_echo2 print_backtrace();@#
			}
			else {
				echo ('Rolling back transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>');
                #@sql_echo2 print_backtrace();@#
			}
		}//@#

		@$GLOBALS['transactionnesting']--;
		$n=null;
		return $n;
	}

    #@php5
    function saveRegisteredObjects() {
    	//ActionDispatcher::ExecuteDeferredEvents();

            try {
                $toRollback = array();
                while(!empty($this->registeredObjects)){
                    $ks = array_keys($this->registeredObjects);
                    $elem =& $this->registeredObjects[$ks[0]];
                    unset($this->registeredObjects[$ks[0]]);
                    $toRollback[] =& $elem;
                    $this->save($elem);
                }
            }
            catch (Exception $e) {
                $this->registeredObjects = $toRollback;
                $e->raise();
            }
    }//@#

    #@php4
    function saveRegisteredObjects() {
        //ActionDispatcher::ExecuteDeferredEvents();

            $toRollback = array();
            while(!empty($this->registeredObjects)){
                $ks = array_keys($this->registeredObjects);
                $elem =& $this->registeredObjects[$ks[0]];
                unset($this->registeredObjects[$ks[0]]);
                $toRollback[] =& $elem;
				#@persistence_echo echo 'saving '.$elem->printString().'<br/>';@#
                $e =& $this->save($elem);
                if (is_exception($e)) {
                    $this->registeredObjects = $toRollback;
                    return $e->raise();
                }
            }
			PersistentObject::CollectCycles();
    }//@#

	function rollback() {
		if ($this->rollback_on_error) return;
        return $this->primRollback();
	}

    function primRollback() {
		#@gencheck if (@$GLOBALS['transactionnesting'] <= 0)
        {
          print_backtrace('Error: trying to rollback a non existing transaction');
        }//@#

        #@sql_echo echo ( 'Rolling back transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>');@#
        #@sql_echo2 print_backtrace();@#

        if (@$GLOBALS['transactionnesting'] == 1) {
			$this->rollbackTransaction();
		}
		else {
			#@sql_echo2 print_backtrace('Setting rollback in true <br/>');@#

			$this->rollback=true;
		}

		@$GLOBALS['transactionnesting']--;
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
	function getLastId(){
		return $this->driver->getLastId();
	}
	function getRowsAffected(&$result){
		return $this->driver->getRowsAffected($result);
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

    function openDatabase($persistent) {
    	return $this->driver->openDatabase($persistent);
    }
    function closeDatabase() {
      return $this->driver->closeDatabase();
    }

    function fetchArray($res) {
		return $this->driver->fetchArray($res);
	}

	function query($q, $persistent=false) {
		return $this->driver->query($q, $persistent);
	}

	function batchExec($sqls) {
		return $this->driver->batchExec($sqls);
	}

	#@php4
    function &save(&$object) {
		$db=&DBSession::Instance();
		$db->registerSave($object);
        $e =& $object->save();
        if (is_exception($e))
        {
            if ($db->rollback_on_error) {
				$db->primRollback();
			}
            return $e->raise();
		}
		return $object;
	}//@#

    #@php5
    function &save(&$object) {
        $db=&DBSession::Instance();
        $db->registerSave($object);
        try {
            $e =& $object->save();
        }
        catch (Exception $e) {
            if ($db->rollback_on_error) {
                $db->primRollback();
            }
            return $e->raise();
        }
    }//@#

	#@php5
    function &delete(&$object) {
		$this->registerDelete($object);

		try {
            $e =& $object->delete();
		} catch (Exception $e)
        {
			if ($this->rollback_on_error) {
				$this->primRollback();
			}
            $e->raise();
		}
	}//@#


    #@php4
    function &delete(&$object) {
        $this->registerDelete($object);

        $e =& $object->delete();
        if (is_exception($e))
        {
            if ($this->rollback_on_error) {
                $this->primRollback();
            }
            return $e->raise();
        } else {
        	return $object;
        }
    }//@#

	function rollbackOnError($b = true) {
		$this->rollback_on_error = $b;
	}
	function tableExists($table){
		return $this->driver->tableExists($table);
	}
	function getTables(){
		return $this->driver->getTables();
	}
	function idFieldType(){
		return $this->driver->idFieldType();
	}
	function referenceType(){
		return $this->driver->referenceType();
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
		#@sql_echo 	echo 'Committing creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->commitMetaFields();
	}

	function rollback() {
		#@sql_echo echo 'Rolling back creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->flushInsert();
	}
}

class UpdateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_echo echo 'Committing update: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->commitMetaFields();
	}

	function rollback() {
		#@sql_echo echo 'Rolling back update: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->flushUpdate();
	}
}

class DeleteObjectDBCommand extends DBCommand {
	function commit() {

	}

	function rollback() {
		#@sql_echo  echo 'Rolling back delete: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->existsObject=TRUE;
	}
}

?>