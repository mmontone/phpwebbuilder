<?php

require_once 'ObjectComponent.class.php';

class EditObjectComponent extends ObjectComponent {
	function EditObjectComponent(&$object) {
		parent::ObjectComponent($object);
	}

    function initialize(){
    	$obj =& $this->obj;
    	$this->addComponent(new Label($this->classN), 'className');
    	$this->addComponent(new Label($obj->id->value), 'idN');
    	$this->factory =& new EditComponentFactory;
       	$this->addComponent(new ActionLink($this, 'save', 'save', $n=null), 'save');
       	//$this->addComponent(new ActionLink($this, 'deleteObject', 'delete', $n=null), 'delete');
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

	/*
	function deleteObject(&$fc) {
		$this->obj->delete();
		$this->callback('refresh');
	}
	*/
}

?>