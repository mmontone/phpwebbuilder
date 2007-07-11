<?php


#@preprocessor Compiler::usesClass(__FILE__, constant('db_driver'));@#

$prepared_to_save = array ();

class DBSession {
	var $commands = array (); // Undoable commands
	var $registeredObjects = array ();
	var $lastSQL;
	var $lastError;
	var $nesting = 0;
	var $rollback_on_error = false;
	var $rollback=false;

	function DBSession() {
		// We register a function for cleaning up things when a request finishes
		pwb_register_shutdown_function('dbsession', new FunctionObject($this, 'shutdown'));
	}

	/*
	 * The per request cleaner function
	 */
	function & shutdown() {
		/*
		if ($this->committing) {
			print_backtrace_and_exit('DB Error: rolling back transaction. committing: ' . print_r($this->committing, true) . ' transaction started: ' . print_r($this->in_transaction, true));
			$this->rollbackTransaction();
			$this->committing = false;
			$a = array ();
			$this->registeredObjects = & $a;
		}

        $n = null;
        return $n;*/
	}

	/*
	 * Transaction nesting related methods
	 */

	function inTransaction() {
		return $this->nesting > 0;
	}

	function incrementTransactionNesting() {
		$this->nesting++;
	}

	function getTransactionNesting() {
		return $this->nesting;
	}

	function decrementTransactionNesting() {
		$this->nesting--;
	}

	/*
	 * The rolling back of nested transactions
	 */
	function rollback() {
		#@sql_echo echo ( 'Rolling back transaction ('. $this->getTransactionNesting() . ')<br/>');@#
		#@sql_echo2 print_backtrace();@#

		if ($this->rollback_on_error) return;
        return $this->primRollback();
	}

	function primRollback() {
		#@gencheck if ($this->getTransactionNesting() <= 0)
        {
          print_backtrace('Error: trying to rollback a non existing transaction');
        }//@#

        #@sql_echo echo ( 'Rolling back transaction ('. $this->getTransactionNesting() . ')<br/>');@#
        #@sql_echo2 print_backtrace();@#

        if ($this->getTransactionNesting() == 1) {
			$this->rollbackTransaction();
		}
		else {
			#@sql_echo2 print_backtrace('Setting rollback in true <br/>');@#

			$this->rollback=true;
		}

		$this->decrementTransactionNesting();
	}

	function commit() {
		return $this->commitRegisteredObjects($this->registeredObjects);
	}

	/*
	 * Primitive DB operations and DB commands
	 */

	function addCommand(& $command) {
		#@sql_echo echo 'Adding command ' . $command->debugPrintString() . '<br/>';@#
		$this->commands[] = & $command;
	}

	function registerSave(& $object) {
		if ($object->existsObject()) {
			$this->addCommand(new UpdateObjectDBCommand($object));
		} else {
			$this->addCommand(new CreateObjectDBCommand($object));
		}
	}

	function registerDelete(& $object) {
		$this->addCommand(new DeleteObjectDBCommand($object));
	}

	function beginTransaction() {
		if (!$this->inTransaction()) {
			$this->driver->beginTransaction();
			#@sql_echo echo 'Beggining transaction<br/>';@#
			#@sql_echo2 print_backtrace();@#
		}
		$this->incrementTransactionNesting();
	}

	function commitTransaction() {
		#@sql_echo echo 'Committing transaction (committing commands)<br/>';@#
		$this->driver->commit();

		foreach (array_keys($this->commands) as $c) {
			$cmd = & $this->commands[$c];
			$cmd->commit();
		}

		$this->commands = array ();

		$n = array ();
		$this->registeredObjects = & $n;
	}

	function rollbackTransaction() {
		#@sql_echo print_backtrace( 'Rolling back transaction (reverting commands)<br/>');@#
		$this->driver->rollback();
		// We want to apply rollbacks in order
		$cmds = array_reverse($this->commands);
		foreach (array_keys($cmds) as $c) {
			$cmd = & $this->commands[$c];
			$cmd->rollback();
		}

		$this->commands = array ();
	}

	/*
	 * Persistence by reacheability protocol
	 */
	function registerObject(& $object) {
		#@persistence_echo echo 'Registering ' . $object->debugPrintString() . ' in ' . print_object($this) . '<br/>';@#
		$set = isset ($this->registeredObjects[$object->getInstanceId()]);
		$this->registeredObjects[$object->getInstanceId()] = & $object;
		$object->toPersist = true;

		if (!$set && !$object->existsObject) {
			$object->registerCollaborators();
		}
	}

	// Commits the modified objects in a transaction
	function commitInTransaction() {
		// Thoughts: explicit calls to CommitInTransaction is semantically irrelevant in the presence of local memory transactions.
		// Now, we may have commit policies if we wanted. For example, we may commit on shutdown and forget about
		// doing explicit commits.
		//                          -- marian

		#@persistence_echo echo 'Committing  global DB transaction</br>';@#
		$this->beginTransaction();
		$this->commit();
	}

	/* This method is called from Memory Transactions and not explicitely by the user */
	function commitMemoryTransaction(& $transaction) {
		#@persistence_echo echo 'Committing  ' . $object->debugPrintString() . '</br>';@#
		$this->beginTransaction();
		$this->commitRegisteredObjects($transaction->registeredObjects);
	}

	// It is called by ObjectsCreators when they are not able to create the object
	// because of an error in the DB (ex. a DB restriction).
	// So, ObjectCreators call them, and ObjectEditors don't.
	// A possible improvement?? would be to detect whether an object creation was going to
	// be commited. If that is true, then we unregisterAllObjects. If not, then we don't.
	// That way, ObjectEditors and Creators would use the same Commiting Transaction/Error Handling interface.
	//                          --marian
	function unregisterAllObjects() {
		// We have to set all objects isPersisted to false so that they get registered for
		// persistence again afterwards --marian

		#@persistence_echo echo 'Unregistering all objects(' . count($this->registeredObjects). ')</br>';@#
		foreach (array_keys($this->registeredObjects) as $i) {
			#@persistence_echo echo 'Unpersisting ' . $this->registeredObjects[$i]->debugPrintString() . '<br/>';@#
			$this->registeredObjects[$i]->toPersist = false;
		}
		$a = array ();
		$this->registeredObjects = & $a;
	}

	function unregisterObject(& $object) {
		$db = & DBSession :: Instance();
		#@persistence_echo echo 'Unregistering object ' . $object->debugPrintString() . '</br>';@#
		unset ($db->registeredObjects[$object->getInstanceId()]);
		$object->toPersist = false;
	}

	function commitRegisteredObjects(& $registeredObjects) {
		#@gencheck if ($this->getTransactionNesting() <= 0)
        {
		  print_backtrace('Error: trying to commit a non existing transaction');
		}//@#

		#@persistence_echo echo 'Commiting '.$this->getTransactionNesting().'<br/>';@#
        if ($this->getTransactionNesting() == 1) {
			if (!$this->rollback) {
				#@sql_echo echo 'Commiting transaction ('. $this->getTransactionNesting() . ')<br/>';@#
                #@sql_echo2 print_backtrace();@#
				$this->saveRegisteredObjects($registeredObjects);
				$this->commitTransaction();
			}
			else {
				#@sql_echo	echo ('Rollback transaction ('. $this->getTransactionNesting() . ')<br/>');@#
                #@sql_echo2 print_backtrace();@#
				$this->rollbackTransaction();
			}
		}
		#@sql_echo
		else {
			if (!$this->rollback) {
				echo ('Commiting transaction ('. $this->getTransactionNesting() . ')<br/>');
                #@sql_echo2 print_backtrace();@#
			}
			else {
				echo ('Rolling back transaction ('. $this->getTransactionNesting() . ')<br/>');
                #@sql_echo2 print_backtrace();@#
			}
		}//@#

		$this->decrementTransactionNesting();
	}

	#@php5
	function saveRegisteredObjects(& $registeredObjects) {
		global $prepared_to_save;
		try {
			$toRollback = $registeredObjects;

			while (!empty ($registeredObjects)) {
				$ks = array_keys($registeredObjects);
				$elem = & $registeredObjects[$ks[0]];
				unset ($registeredObjects[$ks[0]]);

				if (!isset ($prepared_to_save[$elem->getInstanceId()])) {
					$prepared_to_save[$elem->getInstanceId()] = true;
					#@persistence_echo echo 'Preparing to save: ' . $elem->debugPrintString() . '<br/>';@#
					$elem->prepareToSave();
				}
				$this->save($elem);
			}
			PersistentObject :: CollectCycles();
		} catch (DBError $e) {
			foreach (array_keys($toRollback) as $i) {
				#@persistence_echo echo 'Registering back: ' . $toRollback[$i]->debugPrintString() . '<br/>';@#
				$registeredObjects[$toRollback[$i]->getInstanceId()] = & $toRollback[$i];
			}

			$e->raise();
		}
	}
	//@#

	#@php4
	function saveRegisteredObjects(& $registeredObjects) {
		global $prepared_to_save;
		$toRollback = $registeredObjects;

		while (!empty ($registeredObjects)) {
			$ks = array_keys($registeredObjects);
			$elem = & $registeredObjects[$ks[0]];
			unset ($registeredObjects[$ks[0]]);

			if (!isset ($prepared_to_save[$elem->getInstanceId()])) {
				$prepared_to_save[$elem->getInstanceId()] = true;
				#@persistence_echo echo 'Preparing to save: ' . $elem->debugPrintString() . '<br/>';@#
				$elem->prepareToSave();
			}
			if (is_exception($e =& $this->save($elem))){
				foreach (array_keys($toRollback) as $i) {
					#@persistence_echo echo 'Registering back: ' . $toRollback[$i]->debugPrintString() . '<br/>';@#
					$registeredObjects[$toRollback[$i]->getInstanceId()] = & $toRollback[$i];
				}

				return $e->raise();
			}
		}
		PersistentObject :: CollectCycles();
	}
	//@#

	function & Instance() {
		$slot = 'db_session';
		if (!Session :: isSetAttribute($slot)) {
			$dbsession_class = 'DBSession';
			if (defined('dbsession_class')) {
				$dbsession_class = constant('dbsession_class');
			}
			$driver_class = constant('db_driver');
			$dbsession = & new $dbsession_class;
			$dbsession->driver = & new $driver_class ($dbsession);
			Session :: setAttribute($slot, $dbsession);
		}
		return Session :: getAttribute($slot);
	}

	function lastError() {
		$last_error = & DBSession :: GetLastError();
		return $last_error->getMessage();
	}

	function & GetLastError() {
		$db = & DBSession :: instance();
		return $db->lastError;
	}

	function lastSQL() {
		$db = & DBSession :: instance();
		return $db->lastSQL;
	}

	function & SQLExec($sql, $getID, & $obj, & $rows) {
		return $this->driver->SQLExec($sql, $getID, $obj, $rows);
	}
	function getLastId() {
		return $this->driver->getLastId();
	}
	function getRowsAffected(& $result) {
		return $this->driver->getRowsAffected($result);
	}
	function queryDB($query) {
		return $this->driver->queryDB($query);
	}

	function setLastError(& $error) {
		$this->lastError = & $error;
	}

	function & getLastErr() {
		return $this->lastError;
	}

	function clearLastSQL() {
		$this->setLastSQL('');
		if ($this->lastError) {
			$this->lastError->sql = '';
			$this->lastError->backtrace = '';
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

	function & query($q, $persistent = false) {
		return $this->driver->query($q, $persistent);
	}

	function batchExec($sqls) {
		return $this->driver->batchExec($sqls);
	}
	#@php5
	function & save(& $object) {
		$db = & DBSession :: Instance();
		//$db->beginTransaction();
		$db->registerSave($object);
		try {
			$object->save();
			return $object;
		} catch (Exception $e) {
			$this->rollbackTransaction();

			return $e->raise();
		}
	}
	//@#
	#@php4
	function & save(& $object) {
		$db = & DBSession :: Instance();
		//$db->beginTransaction();
		$db->registerSave($object);
		if (is_exception($e =& $object->save())){
			$this->rollbackTransaction();
			return $e->raise();
		}
		return $object;
	}
	//@#

	#@php5
	function & delete(& $object) {
		$this->beginTransaction();
		$this->registerDelete($object);

		try {
			$object->delete();
			return $object;
		} catch (Exception $e) {
			$this->rollbackTransaction();
			return $e->raise();
		}
	}
	//@#
	#@php4
	function & delete(& $object) {
		$this->beginTransaction();
		$this->registerDelete($object);

		if (is_exception($e =& $object->delete())){
			$this->rollbackTransaction();
			return $e->raise();
		}
		return $object;
	}
	//@#
	function tableExists($table) {
		return $this->driver->tableExists($table);
	}
	function getTables() {
		return $this->driver->getTables();
	}
	function idFieldType() {
		return $this->driver->idFieldType();
	}
	function referenceType() {
		return $this->driver->referenceType();
	}

	function printString() {
		return '['  . ucfirst(get_class($this)) . ' transaction nesting: ' . $this->getTransactionNesting() . ']';
	}
}

class DBSessionInstance {
	function BeginTransaction() {
		$db =& DBSession::Instance();
		$db->beginTransaction();
	}

	function Commit() {
		$db =& DBSession::Instance();
		$db->commit();
	}

	function Rollback() {
		$db =& DBSession::Instance();
		$db->rollback();
	}

	function CommitInTransaction() {
		$db =& DBSession::Instance();
		$db->commitInTransaction();
	}

	function CommitMemoryTransaction(&$transaction) {
		$db =& DBSession::Instance();
		$db->commitMemoryTransaction($transaction);
	}

	function UnregisterAllObjects() {
		$db =& DBSession::Instance();
		$db->unregisterAllObjects();
	}
}

class DBCommand {
	var $object;

	function DBCommand(& $object) {
		$this->object = & $object;
	}

	function commit() {
		print_backtrace_and_exit('Subclass responsibility');
	}

	function rollback() {
		print_backtrace_and_exit('Subclass responsibility');
	}

	function & getObject() {
		return $this->object;
	}

	function debugPrintString() {
		return '[' . getClass($this) . ' target: ' . $this->object->debugPrintString() . ']';
	}
}

class CreateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_echo 	echo 'Committing creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->primitiveCommitChanges();
	}

	function rollback() {
		#@sql_echo echo 'Rolling back creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->flushInsert();
	}
}

class UpdateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_echo echo 'Committing update: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->primitiveCommitChanges();
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

		$this->object->existsObject = TRUE;
	}
}
?>