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
    	$this->call($qd =& QuestionDialog::create('Are you sure you want to cancel your changes?'));
    	$qd->registerCallbacks(array('on_yes' => new FunctionObject($this, 'cancelConfirmed'), 'on_no' => new FunctionObject($this, 'cancelRejected')));
    }

    function cancelRejected() {

    }

    function cancelConfirmed() {
    	$this->obj->flushChanges();
    	$this->callback('cancel');
    }

    function save(){
    	if (!$this->obj->validateAll()) {
    		$this->obj->commitChanges();
    		$this->callbackWith('object_edited', $this->obj);
    	} else {
    		$this->triggerEvent('invalid', $this->obj);
    		$this->displayValidationErrors($this->obj->validation_errors);
    	}
    }

    function displayValidationErrors($errors) {
		$this->addComponent(new ValidationErrorsDisplayer($errors), 'validation_errors');
	}

	function deleteObject(&$obj) {
		$this->call($qd =& QuestionDialog::create('Are you sure that you want to delete the object?'));
		$qd->registerCallbacks(array('on_yes' => new FunctionObject($this, 'deleteConfirmed',array('object' => $obj)), 'on_no' => new FunctionObject($this, 'deleteRejected')));
	}

	function deleteConfirmed($params, $fparams) {
		$obj =& $fparams['object'];
		$ok = $obj->delete();
		$this->callback('refresh');
	}

	function deleteRejected() {

	}
}

?>