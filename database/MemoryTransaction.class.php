<?php

class MemoryTransaction {
	var $modifications = array ();
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

		$metaclass = $this->metaclass;
		$this->metaclass =&  new $metaclass;

		$this->metaclass->initializeMemoryTransaction();
		#@tm_echo echo $this->debugPrintString() . ' initialized<br/>';@#
	}

	function isEmpty() {
		return count($this->modifications) == 0;
	}

	function isActive() {
		return $this->active;
	}

	function rollback() {
		if (!$this->isActive()) {
			print_backtrace_and_exit('You cannot rollback an inactive transaction: ' . $this->debugPrintString());
		}

		#@tm_echo print_backtrace('Rolling back ' . $this->debugPrintString() . '<br/>');@#

		// We need too rollback modifications in order
		$original_modifications = array_reverse($this->modifications);
		foreach (array_keys($original_modifications) as $key) {
			$mod = & $original_modifications[$key];
			$mod->rollback();
		}

		$a = array ();
		$this->modifications = & $a;

		$this->active = false;

		$this->metaclass->rollbackMemoryTransaction();

		#@tm_echo echo $this->debugPrintString() . ' rolled back<br/>';@#
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

		$this->metaclass->commitMemoryTransaction();

		// We remove modifications, contrary to saveObjectsInTransaction
		$a = array ();
		$this->modifications = & $a;

		$this->active = false;
	}

	#@php5
	function saveObjectsInTransaction() {
		// We save our registered objects in a transaction, but we don't remove the commands.
		// In a threaded implementation, object changes should be registered in memory transactions, and not globally in
		// the DBSession

		$db = & DBSession :: Instance();
		$db->beginTransaction();
		try {
			$db->saveRegisteredObjects();
			$db->commitTransaction();
		} catch (DBError $e) {
			$db->rollbackTransaction();
			$e->raise();
		}
	}
	//@#

	#@php4
	function saveObjectsInTransaction() {
		// We save our registered objects in a transaction, but we don't remove the commands.
		// In a threaded implementation, object changes should be registered in memory transactions, and not globally in
		// the DBSession

		$db = & DBSession :: Instance();
		$db->beginTransaction();
		if (is_exception($e = & $db->saveRegisteredObjects())) {
			$db->rollbackTransaction();
			$e->raise();
		}
		else {
			$db->commitTransaction();
		}
	}
	//@#

	function registerFieldModification(& $mod) {
		#@tm_echo echo 'Registering ' . $mod->debugPrintString() . ' in ' . $this->debugPrintString() . '<br/>';@#
		if (!isset ($this->modifications[$mod->getHash()])) {
			$this->modifications[$mod->getHash()] = & $mod;
		} else {
			#@tm_echo echo $mod->debugPrintString() . ' already registered in ' . $this->debugPrintString() . '<br/>';@#
		}
	}

	function debugPrintString() {
		return print_object($this, ' modifications: ' . count($this->modifications) . ' thread: ' . $this->thread->debugPrintString() . ' metaclass: ' . print_object($this->metaclass));
	}
}

class DBMemoryTransactionMetaclass {
	function initializeMemoryTransaction() {
		DBSessionInstance :: BeginTransaction();
	}

	function commitMemoryTransaction() {
		$db =& DBSession::Instance();
		$db->commitTransaction(new FunctionObject($db, 'saveRegisteredObjects'));
	}

	function rollbackMemoryTransaction() {
		DBSessionInstance :: RollbackTransaction();
	}
}

class StandardMemoryTransactionMetaclass extends DBMemoryTransactionMetaclass {}

class InMemoryTransactionMetaclass {
	function initializeMemoryTransaction() {

	}

	function commitMemoryTransaction() {

	}

	function rollbackMemoryTransaction() {

	}
}

class GlobalMemoryTransactionMetaclass {
	#@php5
	function saveObjectsInTransaction() {
		// We save our registered objects in a transaction, but we don't remove the commands.
		// In a threaded implementation, object changes should be registered in memory transactions, and not globally in
		// the DBSession

		$db = & DBSession :: Instance();
		$db->beginTransaction();
		try {
			$db->saveRegisteredObjects();
			$db->commitTransaction();
		} catch (DBError $e) {
			$db->rollbackTransaction();
			$e->raise();
		}
	}
	//@#

	#@php4
	function saveObjectsInTransaction() {
		// We save our registered objects in a transaction, but we don't remove the commands.
		// In a threaded implementation, object changes should be registered in memory transactions, and not globally in
		// the DBSession

		$db = & DBSession :: Instance();
		$db->beginTransaction();
		if (is_exception($e = & $db->saveRegisteredObjects())) {
			$db->rollbackTransaction();
			$e->raise();
		}
		else {
			$db->commitTransaction();
		}
	}
	//@#
	function unregisterAllObjects() {
		// TODO: make threaded
		DBSession :: unregisterAllObjects();
	}
}

class ThreadedMemoryTransactionMetaclass {
	var $registered_objects = array ();

	function registerObject(& $object) {
		#@persistence_echo echo 'Registering ' . $object->debugPrintString() . ' in ' . $this->debugPrintString() .'<br/>';@#
		$set = isset ($this->registeredObjects[$object->getInstanceId()]);
		$this->registeredObjects[$object->getInstanceId()] = & $object;
		$object->toPersist = true;

		if (!$set && !$object->existsObject) {
			$object->registerCollaborators();
		}
	}
}

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