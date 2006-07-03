<?php

class PersistentObjectEditor extends PersistentObjectPresenter {
    function initialize(){
    	$obj =& $this->obj;
    	$this->addComponent(new Label($this->classN), 'className');
    	$this->addComponent(new Label($obj->id->getValue()), 'idN');
    	$this->factory =& new EditorFactory;
       	$this->addComponent(new ActionLink($this, 'save', 'save', $n=null), 'save');
		PermissionChecker::addComponent($this,
					new ActionLink($this, 'deleteObject', 'delete', $obj),
					new FunctionObject(User::logged(), 'hasPermissions', array(getClass($obj).'=>Delete', '*',getClass($obj).'=>*'))
					,'delete');
       	$this->addComponent(new ActionLink($this, 'cancel', 'cancel', $n), 'cancel');
		parent::initialize();
    }

    function cancel() {
    	if ($this->obj->isModified()) {
    		$this->confirmCancel();
    	}
    	else
    		$this->cancelConfirmed();
    }

    function confirmCancel() {
    	$this->call(new QuestionDialog('Are you sure you want to cancel your changes?', array('on_yes' => new FunctionObject($this, 'cancelConfirmed'), 'on_no' => new FunctionObject($this, 'cancelRejected'))));
    }

    function cancelRejected() {

    }

    function cancelConfirmed() {
    	$this->obj->flushChanges();
    	$this->callback('cancel');
    }

    function save(){
    	$error_msgs = array();
    	if ($this->validate($this->obj, $error_msgs)) {
    		$this->obj->commitChanges();
    		$this->callbackWith('object_edited', $this->obj);
    	} else {
    		$this->triggerEvent('invalid', $this->obj);
    		$this->displayValidationErrors($error_msgs);
    	}
    }

    function validate(&$object, &$error_msgs) {
		return $object->validate($error_msgs);
    }

	function displayValidationErrors($error_msgs) {
		$this->addComponent(new ValidationErrorsDisplayer($error_msgs), 'validation_errors');
	}

	function deleteObject(&$obj) {
		$this->call(new QuestionDialog('Are you sure that you want to delete the object?', array('on_yes' => new FunctionObject($this, 'deleteConfirmed', $obj), 'on_no' => new FunctionObject($this, 'deleteRejected')), $obj));
	}

	function deleteConfirmed(&$obj) {
		$ok = $obj->delete();
		$this->refresh();
	}

	function deleteRejected() {

	}
}

?>