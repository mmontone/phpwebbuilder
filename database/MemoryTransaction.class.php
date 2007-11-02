<?php

class MemoryTransaction {
	var $modifications = array ();
	var $objects = array ();
	var $thread;
	var $active = true;
	var $metaclass = 'StandardMemoryTransactionMetaclass';
	var $transaction_queries = array();
	var $commands = array (); // Undoable commands

	function MemoryTransaction(& $thread, $options = array()) {
		$this->thread = & $thread;

		/* Options:
		 * metaclass : sets the metaclass. Values: StandardMemoryTransactionMetaclass, InMemoryTransactionMetaclass, DBMemoryTransactionMetaclass
		 */
		foreach($options as $key => $value) {
			$this->$key = $value;
		}

		$this->id =& $thread->getLiveId();
		$this->parent =& $thread->getMemoryTransaction();
		if ($this->parent==null){$this->parent=& DBSession::Instance();}
		$metaclass = $this->metaclass;
		$this->metaclass =&  new $metaclass;

		$this->metaclass->initializeMemoryTransaction($this);
		#@tm_echo echo $this->debugPrintString() . ' initialized<br/>';@#
	}
	function getId(){
		return $this->id;
	}

	function isEmpty() {
		return count($this->modifications) == 0;
	}

	function isActive() {
		return $this->active;
	}
	function rollback() {
		$this->cancel();
	}
	function cancel() {
		if (!$this->isActive()) {
			print_backtrace_and_exit('You cannot cancel an inactive transaction: ' . $this->debugPrintString());
		}

		#@tm_echo print_backtrace('Cancelling transaction ' . $this->debugPrintString() . '<br/>');@#

		// We need too rollback modifications in order
		$this->metaclass->cancelMemoryTransaction($this);
		$this->rollingBack=true;
		$original_modifications = array_reverse($this->modifications);
		foreach (array_keys($original_modifications) as $key) {
			$mod = & $original_modifications[$key];
			$mod->rollback();
		}
		$this->rollingBack=false;
		/*$original_objects = array_reverse($this->objects);
		foreach (array_keys($original_objects) as $key) {
			$mod = & $original_objects[$key];
			$mod->rollback();
		}*/

		$this->cleanUp();

		$this->active = false;


		#@tm_echo echo $this->debugPrintString() . ' rolled back<br/>';@#
	}
	function cleanUp(){
		$a = array ();
		$this->modifications = & $a;
		$b = array ();
		$this->objects = & $b;
		$c = array ();
		$this->transaction_queries = & $c;
		$d = array ();
		$this->commands = & $d;
	}
	function pause(){
		$this->metaclass->cancelMemoryTransaction($this);
		#@tm_echo echo $this->debugPrintString() . ' paused<br/>';@#
	}
	function restart(){
		$this->metaclass->initializeMemoryTransaction($this);
		#@tm_echo echo $this->debugPrintString() . ' started<br/>';@#
	}

	function commit() {
		// As MySQL does not support nested transactions, we can only register all object modifications
		// on a root memory transactions and commit that one. Problems related to the lack of nested transactions
		// are: 1) Non root memory transaction cannot count on db restrictions (example: repeated key fields restrictions).
		// 2) We can only commit and rollback all the changes at once
		//                                                             -- marian
		if (!$this->isActive()) {
			print_backtrace_and_exit('Transaction already commited ' . $this->debugPrintString());
		}

		#@tm_echo echo 'Committing ' . $this->debugPrintString() . '<br/>';@#

		/* First we call the metaclass. There may be exceptions. If no exception ocurred, we remove modifications
		   and deactivate the transaction */

		$this->parent->registerAllModifications($this);
		$this->rollback();

		$this->active = false;
	}

	#@php5
	function saveObjectsInTransaction() {
		// We save our registered objects in a transaction, but we don't remove the commands.
		// In a threaded implementation, object changes should be registered in memory transactions, and not globally in
		// the DBSession
		if ($this->saving) return;
		$this->saving = true;
		/*$db = & DBSession :: Instance();
		$db->beginTransaction();
		try {
			$db->saveRegisteredObjects();
			$db->commitTransaction();
		} catch (DBError $e) {
			$db->rollbackTransaction();
			$e->raise();
		}*/
		$this->saving = false;
	}
	//@#

	#@php4
	function saveObjectsInTransaction() {
		// We save our registered objects in a transaction, but we don't remove the commands.
		// In a threaded implementation, object changes should be registered in memory transactions, and not globally in
		// the DBSession
		if ($this->saving) return;
		$this->saving = true;
		/*$db = & DBSession :: Instance();
		$db->beginTransaction();
		if (is_exception($e = & $db->saveRegisteredObjects())) {
			$db->rollbackTransaction();
			$e->raise();
		}
		else {
			$db->commitTransaction();
		}*/
		$this->saving = false;
	}
	//@#
	function registerObject(& $obj) {
		#@tm_echo echo 'Registering ' . $obj->debugPrintString() . ' in ' . $this->debugPrintString() . '<br/>';@#
		if ($this->rollingBack) return;
		$db =& DBSession::Instance();
		$db->save($obj);
		if (!isset ($this->objects[getClass($obj).$obj->getId()])) {
			$db->registerObject($obj);
			$this->objects[getClass($obj).$obj->getId()] = & $obj;
		} else {
			#@tm_echo echo $obj->debugPrintString() . ' already registered in ' . $this->debugPrintString() . '<br/>';@#
		}
	}
	function rebuild(){
		$objs = $this->commands;
		$count = count($objs);
		#@tm_echo echo 'Rebuilding ' . $this->debugPrintString() . '<br/>';@#
		for($i=0;$i<$count;$i++){
			$objs[$i]->rollback();
		}
	}
	function registerFieldModification(& $mod) {
		#@tm_echo echo 'Registering ' . $mod->debugPrintString() . ' in ' . $this->debugPrintString() . '<br/>';@#
		if (!isset ($this->modifications[$mod->getHash()])) {
			$this->modifications[$mod->getHash()] = & $mod;
		} else {
			#@tm_echo echo $mod->debugPrintString() . ' already registered in ' . $this->debugPrintString() . '<br/>';@#
		}
	}
	function registerAllModifications(&$trans){
		#@tm_echo echo $this->debugPrintString() . ' adding modifications from ' . $trans->debugPrintString() . '<br/>';@#
		$mods = $trans->modifications;
		$count = count($mods);
		for($i=0;$i<$count;$i++){
			$this->modifications[] =& $mods[$i];
		}
		$comms = $trans->commands;
		$count = count($comms);
		for($i=0;$i<$count;$i++){
			$this->commands[] =& $comms[$i];
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
	function runCommands(&$driver){
		foreach($this->commands as $com){
			$com->runCommand($driver);
		}
	}
	function debugPrintString() {
		return print_object($this, ' modifications: ' . count($this->modifications) .' queries: ' . count($this->transaction_queries) . ' thread: ' . $this->thread->debugPrintString() . ' metaclass: ' . print_object($this->metaclass). ' parent: '.$this->parent->debugPrintString());
	}
}

class DBMemoryTransactionMetaclass {
	function initializeMemoryTransaction(&$mt) {
		DBSessionInstance :: BeginMemoryTransaction($mt);
	}

	function commitMemoryTransaction(&$mt) {
		DBSessionInstance :: commitMemoryTransaction($mt);
	}

	function cancelMemoryTransaction(&$mt) {
		DBSessionInstance :: cancelMemoryTransaction($mt);
	}
}

class StandardMemoryTransactionMetaclass extends DBMemoryTransactionMetaclass {}

// Fields modifications

class FieldModification {
	var $field;
	var $value;

	function FieldModification(& $field) {
		$this->field = & $field;
		$this->initialize();
	}
	function initialize() {
		$this->value = & $this->field->getValue();
	}

	function getHash() {
		return getClass($this) . $this->field->getInstanceId();
	}

	function rollback() {
		#@tm_echo echo 'Rolling back ' . $this->debugPrintString() . '<br/>';@#
		$this->field->setValue($this->value);
	}

	function debugPrintString() {
		return print_object($this, ' field: ' . $this->field->debugPrintString() . ' value: ' . $this->value);
	}
}

class IndexFieldModification extends FieldModification {
	var $target;

	function initialize() {
		parent :: initialize();
		$this->target = & $this->field->getTarget();
	}

	function rollback() {
		parent :: rollback();
		if (!is_object($this->target)) {
			$this->field->removeTarget();
		} else {
			$this->field->setTarget($this->target);
		}
	}

	function debugPrintString() {
		return print_object($this, ' field: ' . $this->field->debugPrintString() . ' value: ' . $this->value . ' target: ' . print_object($this->target));
	}
}

class CollectionFieldModification extends FieldModification {
	var $elem;

	function CollectionFieldModification(& $field, & $elem) {
		$this->elem = & $elem;
		parent :: FieldModification($field);
	}

	function initialize() {
	}

	function getHash() {
		return getClass($this) . get_primitive_object_id($this);
	}

	function debugPrintString() {
		return print_object($this, ' field: ' . $this->field->debugPrintString() . ' elem: ' . $this->elem->debugPrintString());
	}
}

class CollectionFieldRemoval extends CollectionFieldModification {
	function rollback() {
		//$this->field->primAdd($this->elem);
		$this->field->collection->refresh();
		$this->field->collection->changed();
	}
}

class CollectionFieldAddition extends CollectionFieldModification {
	function rollback() {
		//$this->field->primRemove($this->elem);
		$this->field->collection->refresh();
		$this->field->collection->changed();
	}
}

class RootObjectsCollectionAddition extends CollectionFieldModification {
	function rollback() {
		DBSession :: unregisterObject($this->elem);
	}
}

class RootObjectsCollectionRemoval extends CollectionFieldModification {
	function rollback() {
		// Implement
	}
}
?>