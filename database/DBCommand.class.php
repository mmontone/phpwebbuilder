<?php

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
	function pause(){}
	function start(){}
	function & getObject() {
		return $this->object;
	}
    function getId(){
        return $this->object->getDBId();
    }

	function debugPrintString() {
		return '[' . getClass($this) . ' target: ' . $this->object->debugPrintString() . ']';
	}
    function isDeletion(){
        return false;
    }
}

class CreateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_dml_echo 	echo 'Committing creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->primitiveCommitChanges();
	}
	function runCommand(){
		$this->object->insert();
	}
	function rollback() {
		#@sql_dml_echo echo 'Rolling back creation: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->flushInsert();
	}
    function &mergeWith(&$command){
        if ($command->isDeletion()){
            $n=null;
            return $n;
        } else {
            return $this;
        }
    }
}

class UpdateObjectDBCommand extends DBCommand {
	function commit() {
		#@sql_dml_echo echo 'Committing update: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->primitiveCommitChanges();
	}
	function runCommand(){
		$this->object->update();
	}
	function rollback() {
		#@sql_dml_echo echo 'Rolling back update: ' . $this->object->debugPrintString() . '<br />';@#
		$this->object->flushUpdate();
	}
    function &mergeWith(&$command){
        return $command;
    }
}

class DeleteObjectDBCommand extends DBCommand {
	function commit() {

	}
	function runCommand(){
		$this->object->delete();
	}
	function rollback() {
		#@sql_dml_echo  echo 'Rolling back delete: ' . $this->object->debugPrintString() . '<br />';@#

		$this->object->existsObject = TRUE;
	}
    function &mergeWith(&$command){
        return $this;
    }
    function isDeletion(){
        return true;
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