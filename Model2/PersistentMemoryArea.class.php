<?php

class PersistentMemoryArea {
	var $attached_objects;

	function attach(& $object) {
		$this->attached_objects[$object->getId()] = & $object;
	}

	function detach(& $object) {
		unset ($this->attached_objects[$object->getId()]);
	}

	function update(& $object, & $new_object) {
		$object = $new_object;
		$this->save($object);
	}
}

class PersistentMemoryAreaCommitPolicy {}

class PersistentMemoryAreaRequestCommitPolicy extends PersistentMemoryAreaCommitPolicy {}

class PersistentMemoryAreaModificationsCommitPolicy extends PersistentMemoryAreaCommitPolicy {}

class PersistentMemoryAreaTimeCommitPolicy extends PersistentMemoryAreaCommitPolicy {}

?>