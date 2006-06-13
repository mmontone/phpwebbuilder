<?php

class DBPersistenceManager extends PersistenceManager{
	function save(&$object){
		return $object->save();
	}
	function &load($class, $id){
		return PersistentObject::getWithId($class, $id);
	}
}
?>