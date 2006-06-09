<?php

class PersistentObjectEditor extends PersistentObjectPresenter {
	function PersistentObjectEditor(&$object) {
		parent::PersistentObjectPresenter($object);
	}

    function initialize(){
    	$obj =& $this->obj;
    	$this->addComponent(new Label($this->classN), 'className');
    	$this->addComponent(new Label($obj->id->value), 'idN');
    	$this->factory =& new EditorFactory;
       	$this->addComponent(new ActionLink($this, 'save', 'save', $n=null), 'save');
       	$this->addComponent(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
       	$this->addComponent(new ActionLink($this, 'cancel', 'cancel', $n), 'cancel');
		parent::initialize();
    }

    function cancel() {
    	$this->callback('cancel');
    }

    function save(){
    	// Refactor: population shouldn't be necessary
    	$this->populateObject($this->obj);

    	$error_msgs = array();
    	if ($this->validate($this->obj, $error_msgs))
    		$this->callbackWith('object_edited', $this->obj);
    	else
    		$this->displayValidationErrors($error_msgs);
    }

    function populateObject(&$object) {
    	$fs =& $this->fieldNames;
    	foreach($fs as $f){
    		$v = $this->fieldComponents[$f]->getValue();
    		$object->$f->setValue($v);
    	}
    }

    function validate(&$object, &$error_msgs) {
		return $object->validate($error_msgs);
    }

	function displayValidationErrors($error_msgs) {
		$this->addComponent(new ValidationErrorsDisplayer($error_msgs), 'validation_errors');
	}

	function deleteObject(&$fc) {
		$this->call(new QuestionDialog('Are you sure that you want to delete the object?', array('on_yes' => new FunctionObject($this, 'deleteConfirmed', $fc), 'on_no' => new FunctionObject($this, 'deleteRejected')), $fc));
	}

	function deleteConfirmed(&$fc) {
		$ok = $fc->obj->delete();
		$this->refresh();
	}

	function deleteRejected() {

	}
}

?>