<?php

// This is global Memory Transactions manager
// We are not using this at the moment. It is replaced by dynamically scoped transactions
// See Component>>executeCommand

class MemoryTransactionManager {
    var $transactions = array();

    function registerFieldModification($field) {
        if ($this->noTransactions()) {
            #@persistence_echo echo 'There are no memory transactions for modification of: ' . $field->debugPrintString() . '<br/>';//@#
            #@persistence_echo echo 'Creating memory transaction<br/>';//@#

            $this->beginTransaction();
        }

        $current_transaction =& $this->currentTransaction();
        $current_transaction->registerFieldModification($field);
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

    function MemoryTransaction(&$thread) {
    	$this->thread =& $thread;
    }

    function rollback() {
        #@tm_echo echo 'Rolling back ' . $this->debugPrintString() . '<br/>';@#
        // We set current_component to null because we dont want rollback modifications
        // to be registered in the current memory transaction
        defdyn('current_component', $n = null);
        foreach(array_keys($this->modifications) as $key) {
            $mod =& $this->modifications[$key];
            $mod->rollback();
        }
        undefdyn('current_component');
        $a = array();
        $this->modifications =& $a;
        #@tm_echo echo $this->debugPrintString() . ' rolled back<br/>';@#
    }

    function registerFieldModification(&$field) {
        #@tm_echo echo 'Registering ' . $field->debugPrintString() . ' modification in ' . $this->debugPrintString() . '<br/>';@#
        //print_backtrace();
        if (!isset($this->modifications[$field->getInstanceId()])) {
            $this->modifications[$field->getInstanceId()] =& $field->getModificationObject();
        }
        else {
        	#@tm_echo echo 'Modification of ' . $field->debugPrintString() . ' already registered in ' . $this->debugPrintString() . '<br/>';@#
        }
        $this->addModification($field->getModificationObject());
    }

    function addModification(&$mod) {

    }

    function debugPrintString() {
    	return print_object($this, ' modifications: ' . count($this->modifications)  . ' thread: ' . $this->thread->debugPrintString());
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

?>