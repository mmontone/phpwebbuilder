<?php

#@mixin ObjectCreator
{
	function onCreationDo(& $function) {
		$this->onEditionDo($function);
	}

	// In rollbackTransaction, We have to unregister all objects :S. Maybe we should use preconditions in order to avoid this.
	// That is to say: the object is always validated against the database before trying to commit.
	// We have to do this because we don't know which objects to unregister
	// Solution 1: traverse a datastructure (incompatible with objects registering)
	// Solution 2: validate objects so that there are not DB errors
	// Solution 3: this one.
	// In general, validate your objects before committing them to the DB. This method
	// is just for back up.
	//                           -- marian
	function rollbackTransaction() {
		$this->unregisterAllMemoryTransactionObjects();
	}

	function successfulEditionMessage() {
		return 'El objeto ha sido creado con éxito';
	}
} //@#

class ObjectCreator extends ObjectEditor {
	#@use_mixin ObjectCreator@#
	function ObjectCreator($options=array()) {
		parent :: ObjectEditor($this->createObject(), $options);
	}
}

class CommonObjectCreator extends ObjectCreator {
	function CommonObjectCreator($class, $options=array()) {
		$this->objectClass = $class;
		parent :: ObjectCreator($options);
	}
	function createObject() {
		$class = $this->objectClass;
		return new $class;
	}
}

?>