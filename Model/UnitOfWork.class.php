<?php

class UnitOfWork {
	var $new_objects;
	var $dirty_objects;
	var $removed_objects;
	var $commands; // Rollback support? Uhhh...

	function registerNewObject(& $object) {
		$this->new_objects[] = & $object;
	}
}
?>