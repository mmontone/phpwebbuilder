<?php

// This is global Memory Transactions manager
// We are not using this at the moment. It is replaced by dynamically scoped transactions

class MemoryTransactionManager {
    var $transactions = array();

    function registerFieldModification(&$mod) {
        if ($this->noTransactions()) {
            #@persistence_echo echo 'There are no memory transactions for modification: ' . $mod->debugPrintString() . '<br/>';//@#
            #@persistence_echo echo 'Creating memory transaction<br/>';//@#

            $this->beginTransaction();
        }

        $current_transaction =& $this->currentTransaction();
        $current_transaction->registerFieldModification($mod);
    }

    function rollbackCurrentTransaction() {
        $current_transaction =& $this->currentTransaction();
        $current_transaction->rollback();
        array_pop($this->transactions);
    }

    function noTransactions() {
        return empty($this->transactions);
    }

    function &currentTransaction() {
        return $this->transactions[count($this->transactions) - 1];
    }

    function beginTransaction() {
        $this->transactions[] =& new MemoryTransaction;
    }
}

class MemoryTransaction {
    var $modifications = array();
    var $thread;
    var $nesting = -1;
    // Tells whether there's been a db query in the transaction context
    // If that is true, then we have to commit when we rollback.
    var $db_touched = false;
    var $commited=false;

    function MemoryTransaction(&$thread) {
    	$this->thread =& $thread;
    }

    function isEmpty() {
      return count($this->modifications) == 0;
    }

    function rollback() {
      if ($this->commited) {
	       print_backtrace_and_exit('You cannot rollback a commited transaction: ' . $this->debugPrintString());
      }

      #@tm_echo print_backtrace('Rolling back ' . $this->debugPrintString() . '<br/>');@#

     // We need too rollback modifications in order
     $original_modifications = array_reverse($this->modifications);
	foreach(array_keys($original_modifications) as $key) {
	  $mod =& $original_modifications[$key];
	  $mod->rollback();
	}

        $a = array();
        $this->modifications =& $a;

	// If the db was touched, then we have to commit the rollback
	if ($this->db_touched) {
	  $this->commitInTransaction();
	}

	#@tm_echo echo $this->debugPrintString() . ' rolled back<br/>';@#
    }

    function registerFieldModification(&$mod) {
        #@tm_echo echo 'Registering ' . $mod->debugPrintString() . ' in ' . $this->debugPrintString() . '<br/>';@#
        if (!isset($this->modifications[$mod->getHash()])) {
	  $this->modifications[$mod->getHash()] =& $mod;
        }
        else {
        	#@tm_echo echo $mod->debugPrintString() . ' already registered in ' . $this->debugPrintString() . '<br/>';@#
        }
    }

    function debugPrintString() {
    	return print_object($this, ' modifications: ' . count($this->modifications)  . ' thread: ' . $this->thread->debugPrintString());
    }

    // Behaviour that belonged to DBSession
    var $registered_objects = array();

    function registerObject(&$object){
		#@persistence_echo echo 'Registering ' . $object->debugPrintString() . ' in ' . $this->debugPrintString() .'<br/>';@#
		$set = isset($this->registeredObjects[$object->getInstanceId()]);
		$this->registeredObjects[$object->getInstanceId()] =& $object;
		$object->toPersist = true;

		if (!$set && !$object->existsObject){
			$object->registerCollaborators();
		}
	}

	// DBSession>>flushChanges is not needed because we have >>rollback

	function commitInTransaction() {
		// As MySQL does not support nested transactions, we can only register all object modifications
		// on a root memory transactions and commit that one. Problems related to the lack of nested transactions
		// are: 1) Non root memory transaction cannot count on db restrictions (example: repeated key fields restrictions).
		// 2) We can only commit and rollback all the changes at once
		//                                                             -- marian
	  if ($this->commited) {
	    print_backtrace_and_exit('Transaction already commited ' . $this->debugPrintString());
	  }

	  #@persistence_echo echo 'Committing ' . $this->debugPrintString() . '<br/>';@#
	  //DBSession::CommitMemoryTransaction($this);
	  DBSession::CommitInTransaction();

	  // We remove modifications, contrary to saveObjectsInTransaction
	  $a = array();
	  $this->modifications =& $a;

  	  $this->commited = true;
	}
	#@php5
	function saveObjectsInTransaction() {
	  // We save our registered objects in a transaction, but we don't remove the commands.
	  // In a threaded implementation, object changes should be registered in memory transactions, and not globally in
	  // the DBSession

	  $db =& DBSession::Instance();
      $db->beginTransaction();
      try {
            $db->saveRegisteredObjects($db->registeredObjects);
            $this->db_touched = true;
      }
      catch(DBError $e) {
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

	  $db =& DBSession::Instance();
      $db->beginTransaction();
      if (is_exception($e=&$db->saveRegisteredObjects($db->registeredObjects))){
            $db->rollbackTransaction();
            $e->raise();
      }
      $this->db_touched = true;
    }
	//@#
	function unregisterAllObjects() {
	  // TODO: make threaded
	  DBSession::unregisterAllObjects();
	}
}

// Fields modifications

class FieldModification {
    var $field;
    var $value;

    function FieldModification(&$field) {
       $this->field =& $field;
       $this->initialize();
    }
    function initialize() {
        $this->value =& $this->field->getValue();
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
        parent::initialize();
        $this->target =& $this->field->getTarget();
    }


    function rollback() {
        parent::rollback();
        if (!is_object($this->target)) {
        	$this->field->removeTarget();
        }
        else {
            $this->field->setTarget($this->target);
        }
    }

    function debugPrintString() {
        return print_object($this, ' field: ' . $this->field->debugPrintString() . ' value: ' . $this->value . ' target: ' . print_object($this->target));
    }
}

class CollectionFieldModification extends FieldModification {
  var $elem;

  function CollectionFieldModification(&$field, &$elem) {
    $this->elem =& $elem;
    parent::FieldModification($field);
  }

  function initialize() {}

  function getHash() {
    return getClass($this) . get_primitive_object_id($this);
  }

   function debugPrintString() {
     return print_object($this, ' field: ' . $this->field->debugPrintString() . ' elem: ' . $this->elem->debugPrintString());
    }
}

class CollectionFieldRemoval extends CollectionFieldModification {

  /* This is not working at the moment: we disable it
  function rollback() {
    $this->field->add($this->elem);
    }*/

  function rollback() {}
}

class CollectionFieldAddition extends CollectionFieldModification {
  /* This is not working at the moment: we disable it
  function rollback() {
    $this->field->remove($this->elem);
    }*/
  function rollback() {
  }
}

class RootObjectsCollectionAddition extends CollectionFieldModification {
  function rollback() {
    DBSession::unregisterObject($elem);
  }
}

class RootObjectsCollectionRemoval extends CollectionFieldModification {
  function rollback() {
    // Implement
  }
}

?>