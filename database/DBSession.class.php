<?php

#@preprocessor Compiler::usesClass(__FILE__, constant('db_driver'));@#

$prepared_to_save = array ();

/* DBSession can be used at different levels of abstraction. That's because although having for example persistence by
 * reachability, we don't want to lose control (say, not being able to access the driver, or begin and commit transactions).
 *
 * Layers:
 * 1) Driver level: has to do with raw db operations.
 *    Protocol: tableExists, beginTransaction, etc.
 * 2) Objects level: has to do with persisting objects and error handling.
 *    Protocol: beginTransaction, save, delete, commitTransaction, rollbackTransaction.
 *    Implementation related: $rollback_on_error, $rollback, $commands.
 *
 *
 */
class DBSession {
	var $commands = array (); // Undoable commands
	var $registeredObjects = array ();
	var $lastSQL;
	var $lastError;
	var $nesting = 0;
	var $rollback_on_error = false;
	var $rollback=false;
	var $transaction_queries=array();
	var $memoryTransactions=array();

	/*
	 * Transaction nesting related methods
	 */

	function inTransaction() {
		return $this->nesting > 0;
	}

	function incrementTransactionNesting() {
		$this->nesting++;
		#@sql_dml_echo2 print_backtrace('Nesting incremented: ' . $this->nesting);@#
	}

	function getTransactionNesting() {
		return $this->nesting;
	}

	function decrementTransactionNesting() {
		$this->nesting--;
		#@sql_dml_echo2 print_backtrace('Nesting decremented: ' . $this->nesting);@#
	}

	/*
	 * Primitive DB operations and DB commands
	 */

	function addCommand(& $command) {
		#@sql_dml_echo echo print_backtrace('Adding command ' . $command->debugPrintString() . '<br/>');@#
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

	/* Begins a transaction in the db if we are not already in one. If not, then it only
	 * increments the transaction nesting.
	 */
	function beginTransaction() {
		if (!$this->inTransaction()) {
			$this->driver->beginTransaction();
			#@sql_dml_echo echo 'Really Beggining transaction<br/>';@#
			#@sql_dml_echo2 print_backtrace('Really Beggining transaction');@#
		}
		$this->incrementTransactionNesting();
	}

	/* Commits a transaction if we are in the outest. If not, it only decrements the transaction level.
	 * There's an optional parameter to execute some function in case it has to do a commit to the db.
	 */
	function commitTransaction($commit_func = null) {
		#@gencheck if ($this->getTransactionNesting() <= 0)
        {
		  print_backtrace('Error: trying to commit a non existing transaction');
		}//@#

		if ($this->getTransactionNesting() == 1) {
			if (!$this->rollback) {
				#@sql_dml_echo echo 'Commiting transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>';@#
                #@sql_dml_echo2 print_backtrace();@#
				#@php4
				if (!is_null($commit_func)) {
					$e =& $commit_func->call();
					if (is_exception($e)) {
						/* Now this is how we handle errors. We rollback db commands to prevent our objects
					 * from having inconsistent ids or versions.
					 * Finally, we raise the exception for the client
					 * to decide what to do (we do not rollback the transaction or modify the transaction levels, that's
					 * the client responsibility)
					 *            -- marian
					 */
						$this->rollbackDBCommands();
						return $e;
					}
				}//@#
				#@php5
				if (!is_null($commit_func)) {
					try {
						$commit_func->call();
					}
					catch (DBError $e) {
					/* Now this is how we handle errors. We rollback db commands to prevent our objects
					 * from having inconsistent ids or versions.
					 * Finally, we raise the exception for the client
					 * to decide what to do (we do not rollback the transaction or modify the transaction levels, that's
					 * the client responsibility)
					 *            -- marian
					 */
						$this->rollbackDBCommands();
						return $e->raise();
					}
				}//@#
				$this->driver->commit();
				$this->commitDBCommands();
			}
			else {
				#@sql_dml_echo	echo ('Rollback transaction ('. @$GLOBALS['transactionnesting'] . ')<br/>');@#
				$this->rollbackTransaction();
			}
			$this->rollback=false;
		}
		#@sql_dml_echo
		else {
			if (!$this->rollback) {
				echo ('Commiting transaction ('. $this->getTransactionNesting() . ')<br/>');
                #@sql_dml_echo2 print_backtrace();@#
			}
			else {
				echo ('Rolling back transaction ('. $this->getTransactionNesting() . ')<br/>');
                #@sql_dml_echo2 print_backtrace();@#
			}
		}//@#

		$this->decrementTransactionNesting();
	}

	function rollbackTransaction() {
		#@sql_dml_echo echo ( 'Rolling back transaction ('. $this->getTransactionNesting() . ')<br/>');@#
		#@sql_dml_echo2 print_backtrace();@#

		/* If rollback_on_error is true, then the DBSession is in charge of
		 * rolling back the transaction (see DBSession>>save).
		 * That means explicit calls to rollback should not take place as we would be
		 * rollbacking twice. That's why we return in that case.
		 */
		if ($this->rollback_on_error) return;
		if ($this->getTransactionNesting() == 1) {
			#@sql_dml_echo print_backtrace( 'Rolling back transaction (reverting commands)<br/>');@#
			$this->driver->rollback();
			$this->rollbackDBCommands();
			$this->rollback=false;
		}
		else {
			#@sql_dml_echo2 print_backtrace('Setting rollback in true <br/>');@#
			$this->rollback=true;
		}

		$this->decrementTransactionNesting();
	}

	function commitDBCommands() {
		foreach (array_keys($this->commands) as $c) {
			$cmd = & $this->commands[$c];
			$cmd->commit();
		}

		$this->commands = array ();

		$n = array ();
		$this->registeredObjects = & $n;
		$this->cleanUp();
	}

	function rollbackDBCommands() {
		// We want to apply rollbacks in order
		$cmds = array_reverse($this->commands);
		foreach (array_keys($cmds) as $c) {
			$cmd = & $this->commands[$c];
			$cmd->rollback();
		}

		$this->commands = array ();
	}

	/*
	 * Persistence by reachability protocol
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
		$this->commitTransaction(new FunctionObject($this, 'saveRegisteredObjects'));
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

	function commitRegisteredObjects() {
		$this->commitTransaction(new FunctionObject($this, 'saveRegisteredObjects'));
	}

	#@php5
	function saveRegisteredObjects() {
		global $prepared_to_save;
		try {
			$toRollback = $this->registeredObjects;

			while (!empty ($this->registeredObjects)) {
				$ks = array_keys($this->registeredObjects);
				$elem = & $this->registeredObjects[$ks[0]];
				unset ($this->registeredObjects[$ks[0]]);

				$this->save($elem);
			}
			PersistentObject :: CollectCycles();
		} catch (DBError $e) {
			foreach (array_keys($toRollback) as $i) {
				#@persistence_echo echo 'Registering back: ' . $toRollback[$i]->debugPrintString() . '<br/>';@#
				$this->registeredObjects[$toRollback[$i]->getInstanceId()] = & $toRollback[$i];
			}

			$e->raise();
		}
	}
	//@#

	#@php4
	function saveRegisteredObjects() {
		global $prepared_to_save;
		$toRollback = $this->registeredObjects;

		while (!empty ($this->registeredObjects)) {
			$ks = array_keys($this->registeredObjects);
			$elem = & $this->registeredObjects[$ks[0]];
			unset ($this->registeredObjects[$ks[0]]);

			if (is_exception($e =& $this->save($elem))){
				foreach (array_keys($toRollback) as $i) {
					#@persistence_echo echo 'Registering back: ' . $toRollback[$i]->debugPrintString() . '<br/>';@#
					$this->registeredObjects[$toRollback[$i]->getInstanceId()] = & $toRollback[$i];
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
			$dbsession->memoryTransactions[]=&$dbsession;
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
		try {
            $this->registerSave($object);
            if (!isset ($prepared_to_save[$object->getInstanceId()])) {
				$prepared_to_save[$object->getInstanceId()] = true;
				#@persistence_echo echo 'Preparing to save: ' . $elem->debugPrintString() . '<br/>';@#
				$object->prepareToSave();
			}
            $object->save();
            return $object;
        }
        catch (Exception $e) {
        	if ($db->rollback_on_error) {
                $db->primRollback();
            }
            return $e->raise();
        }
	}
	//@#

	#@php4
	function & save(& $object) {
		$this->registerSave($object);
        if (!isset ($prepared_to_save[$object->getInstanceId()])) {
			$prepared_to_save[$object->getInstanceId()] = true;
			#@persistence_echo echo 'Preparing to save: ' . $elem->debugPrintString() . '<br/>';@#
			$object->prepareToSave();
		}
        $e =& $object->save();
        if (is_exception($e))
        {
            if ($db->rollback_on_error) {
				$db->primRollback();
			}
            return $e->raise();
		}
		return $object;
	}
	//@#

	#@php5
	function & delete(& $object) {
		$this->registerDelete($object);

		try {
            $e =& $object->delete();
            return $object;
		} catch (Exception $e) {
			if ($this->rollback_on_error) {
				$this->primRollback();
			}
            $e->raise();
		}
	}
	//@#

	#@php4
	function & delete(& $object) {
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
	}
	//@#

	/* DB Driver layer */
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
	function currentTransaction(){
		$current_component =& getdyn('current_component');
        if (is_object($current_component)) {
        	if (is_object($mt =& $current_component->getMemoryTransaction())){
       			return $mt;
        	}
        }
        return DBSession::Instance();
	}
	function beginMemoryTransaction(&$trans){
		if (count($this->memoryTransactions)==1){
			$this->beginTransaction();
		}
		#@tm_echo echo count($this->memoryTransactions) . ' Memory Transactions active<br/>';@#
		$this->memoryTransactions[]=&$trans;

	}
	function cancelMemoryTransaction(&$trans){
		$nmts = array();
		$miid = $trans->getId();
		#@tm_echo echo count($this->memoryTransactions) . ' Memory Transactions active, removing '.$miid.'<br/>';@#
		for ($i=count($this->memoryTransactions)-1; $i>=0; $i--){
			if ($this->memoryTransactions[$i]->getId()!=$miid){
				$nmts []=&$this->memoryTransactions[$i];
			}
		}
		$this->memoryTransactions =& $nmts;
		$this->rebuildTransactionQueries();
		#@tm_echo echo count($this->memoryTransactions) . ' Memory Transactions active<br/>';@#
		if (count($this->memoryTransactions)==1){
			#@tm_echo echo 'Commiting Memory Transaction<br/>';@#
			$this->commitTransaction();
		}
	}
	function cleanUp(){
		$a = array ();
		$this->modifications = & $a;
		$a = array ();
		$this->objects = & $a;
		$a = array ();
		$this->transaction_queries = & $a;
	}
	function rebuildTransactionQueries() {
		$conn = & $this->driver->openDatabase(false);
		$this->driver->basicRollback($conn);
		$this->driver->new_request=true;
		$this->driver->processTransactionQueries($conn);
	}
	function registerAllModifications(&$trans){
		#@tm_echo echo $this->debugPrintString() . ' adding modifications from ' . $trans->debugPrintString() . '<br/>';@#
		$mods = $trans->modifications;
		$count = count($mods);
		for($i=0;$i<$count;$i++){
			$this->modifications[] =& $mods[$i];
		}
		$objs = $trans->objects;
		$count = count($objs);
		for($i=0;$i<$count;$i++){
			$this->objects[] =& $objs[$i];
		}
		$this->transaction_queries = array_merge($trans->transaction_queries, $this->transaction_queries);
		$trans->cleanUp();
		#@tm_echo echo $this->debugPrintString() . ' final state<br/>'
	}
	function getId(){
		return '';
	}
	function debugPrintString(){
		return 'DBSession';
	}

}

class DBSessionInstance {
	function BeginTransaction() {
		$db =& DBSession::Instance();
		$db->beginTransaction();
	}

	function CommitTransaction() {
		$db =& DBSession::Instance();
		$db->commitTransaction();
	}

	function RollbackTransaction() {
		$db =& DBSession::Instance();
		$db->rollbackTransaction();
	}

	function CommitInTransaction() {
		$db =& DBSession::Instance();
		$db->commitInTransaction();
	}

	function CommitRegisteredObjects() {
		$db =& DBSession::Instance();
		$db->commitRegisteredObjects();
	}

	function CommitMemoryTransaction(&$transaction) {
		$db =& DBSession::Instance();
		$db->commitMemoryTransaction($transaction);
	}
	function BeginMemoryTransaction(&$transaction) {
		$db =& DBSession::Instance();
		$db->beginMemoryTransaction($transaction);
	}
	function CancelMemoryTransaction(&$transaction) {
		$db =& DBSession::Instance();
		$db->cancelMemoryTransaction($transaction);
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
		#@sql_dml_echo 	echo 'Committing creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->primitiveCommitChanges();
	}

	function rollback() {
		#@sql_dml_echo echo 'Rolling back creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->flushInsert();
	}
}

class UpdateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_dml_echo echo 'Committing update: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->primitiveCommitChanges();
	}

	function rollback() {
		#@sql_dml_echo echo 'Rolling back update: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->flushUpdate();
	}
}

class DeleteObjectDBCommand extends DBCommand {
	function commit() {

	}

	function rollback() {
		#@sql_dml_echo  echo 'Rolling back delete: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->existsObject = TRUE;
	}
}

/* PHP4:
 * $db->beginTransaction();
 * $db->beginTransaction();
 * $e =& $db->save($object);
 * if (is_exception($e)) {
 * 		$db->rollbackTransaction();
 * }
 * else {
 * 	$db->commitTransaction();
 * }
 * $db->commitTransaction(); // This rollback if there was an error
 * */

 /* PHP5:
 * $db->beginTransaction();
 * try {
	 * $db->beginTransaction();
	 * try {
	 * 		$db->save($object);
	*			$db->commitTransaction();
	 * }
	 * catch (DBError $e) {
	 * 		$db->rollbackTransaction();
	 * 		$e->raise();
	 * }
	 * }
	 * catch (DBError $e) {
	 * 		$db->rollbackTransaction();
	 * 		$this->showDialog($e);
	 * }
 */

?>