<?php

class MemoryTransaction {
	#@use_mixin TransactionObject@#
	var $thread;
	var $active = true;
	var $metaclass = 'StandardMemoryTransactionMetaclass';

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
	}
	function start(){
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
		$this->dequeue();
	}
	function cancel() {
		$this->dequeue();
	}
	function dequeue() {
		/*if (!$this->isActive()) {
			print_backtrace_and_exit('You cannot cancel an inactive transaction: ' . $this->debugPrintString());
		}*/

		#@tm_echo print_backtrace('Dequeuing transaction ' . $this->debugPrintString() . '<br/>');@#

		// We need too rollback modifications in order
		$this->metaclass->cancelMemoryTransaction($this);
		$this->rollingBack=true;
		$this->rollbackModifications();
		$this->rollingBack=false;
		/*$original_objects = array_reverse($this->objects);
		foreach (array_keys($original_objects) as $key) {
			$mod = & $original_objects[$key];
			$mod->rollback();
		}*/

		$this->cleanUp();

		//$this->active = false;


		#@tm_echo echo $this->debugPrintString() . ' rolled back<br/>';@#
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
		/*if (!$this->isActive()) {
			print_backtrace_and_exit('Transaction already commited ' . $this->debugPrintString());
		}*/

		#@tm_echo echo 'Committing ' . $this->debugPrintString() . '<br/>';@#

		/* First we call the metaclass. There may be exceptions. If no exception ocurred, we remove modifications
		   and deactivate the transaction */

		$this->parent->registerAllModifications($this);
		$this->dequeue();

		//$this->active = false;
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
	function registerFieldModification(& $mod) {
		#@tm_echo echo 'Registering ' . $mod->debugPrintString() . ' in ' . $this->debugPrintString() . '<br/>';@#
		if (!isset ($this->modifications[$mod->getHash()])) {
			$this->modifications[$mod->getHash()] = & $mod;
		} else {
			#@tm_echo echo $mod->debugPrintString() . ' already registered in ' . $this->debugPrintString() . '<br/>';@#
		}
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
		return print_object($this, ' field: ' . $this->field->debugPrintString() . ' value: ' . print_object($this->value));
	}
}

class IndexFieldModification extends FieldModification {
	var $target;

	function debugPrintString() {
		return print_object($this, ' field: ' . $this->field->debugPrintString() . ' value: ' . ($this->value) . ' target: ' . print_object($this->target));
	}
}

class CollectionFieldModification extends FieldModification {
	var $elem;

	function CollectionFieldModification(& $field, & $elem) {
		$this->elem = & $elem;
		$iid =& Session::getAttribute('instance_id');
		$this->__instance_id = ++$iid;
		parent :: FieldModification($field);
	}

	function initialize() {
	}

	function getHash() {
		return getClass($this) . $this->__instance_id;
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