<?php


class ObjectEditor extends ObjectPresenter {
	var $object_edited = false;

	function initialize(){
		$this->beginMemoryTransaction();
        parent::initialize();
		$this->addButtons();
	}

 	function nonCallStop() {
 		if (!$this->object_edited) $this->doFlush();
 	}

	function doFlush(){
        $this->rollbackMemoryTransaction();
	}

    function cancel() {
    	if ($this->object->isModified()) {
    		$this->confirmCancel();
    	}
    	else
    		$this->cancelConfirmed();
    }
	function &chooseFieldDisplayer(&$field){
		return mdcompcall('getFieldEditor', array(&$this,&$field));
	}
    function confirmCancel() {
    	$question =& QuestionDialog::create($this->confirmCancelMessage());
    	$question->registerCallback('on_yes', new FunctionObject($this, 'cancelConfirmed'));
    	$question->registerCallback('on_no', new FunctionObject($this, 'cancelRejected'));
    	$this->call($question);
    }

    function confirmCancelMessage() {
    	return '¿Descartar los cambios realizados?';
    }

    function cancelRejected() {
		$this->triggerEvent('cancel_rejected', $this);
    }

    function cancelConfirmed() {
    	$this->callback($this->cancelCallback());
    }

    function cancelCallback() {
    	return 'cancel';
    }

    function &getSaveLink($text='Save') {
    	return new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'saveObject')));
    }

    function &getCancelLink($text='Cancel') {
		return new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'cancel')));
	}

	function addButtons() {
		$this->addSaveButton();
		$this->addCancelButton();
	}

	function removeButtons() {
		$this->removeSaveButton();
		$this->removeCancelButton();
	}

	function addSaveButton() {
		$this->addComponent($this->getSaveLink('Guardar'), 'save');
	}

	function addCancelButton() {
		$this->addComponent($this->getCancelLink('Cancelar'), 'cancel');
	}

	function removeSaveButton() {
		$this->deleteComponentAt('save');
	}

	function removeCancelButton() {
		$this->deleteComponentAt('cancel');
	}


    #@php4
    function saveObject() {
    	if ($this->validateObject()) {
			$this->saveValidatedObject();
		}
    	else {
            $this->addComponent(new ValidationErrorsDisplayer($this->object->validation_errors), 'validation_errors');
		}
    }//@#

    #@php5
    function saveObject() {
        try {
        	$this->validateObject();
            $this->saveValidatedObject();
        }
        catch (PWBValidationError $e) {
        	$this->addComponent(new ValidationErrorsDisplayer($this->object->validation_errors), 'validation_errors');
        }
    }//@#

    function validateObject() {
    	return $this->object->validateAll();
    }

    function saveError(&$ex) {
    	$dialog =& ErrorDialog::create($ex->getMessage());
		$dialog->onAccept(new FunctionObject($this, 'errorAccepted'));
		$this->call($dialog);
    }

    function saveSuccessful() {
    	$notification =& NotificationDialog::create($this->successfulSaveMessage());
		$notification->onAccept(new FunctionObject($this, 'objectSaved'));
		$this->call($notification);
    }

    function errorAccepted() {

    }

    function objectSaved() {
		$this->callback();
	}

    function successfulSaveMessage() {
    	return 'Los cambios han sido aplicados exitosamente';
    }

    function objectEditedCallback() {
    	return 'object_edited';
    }

    function saveValidatedObject() {
        $this->objectEdited();
        $this->saveSuccessful();
    }

    function objectEdited() {
		$this->object_edited = true;
    	$this->triggerEvent($this->objectEditedCallback(), $this->object);
    }
}


#@defmdf &getFieldEditor[Component](&$field: DataField)
{
	return new Input($field);
}
//@#

#@defmdf &getFieldEditor[Component](&$field: TextArea)
{
	return new TextAreaComponent($field);
}
//@#


#@defmdf &getFieldEditor[Component](&$field: IndexField)
{
	return new Select($field->asValueModel(), new PersistentCollection($field->getDataType()));
}
//@#

#@defmdf &getFieldEditor[Component](&$field: CollectionField)
{
	$oa =& mdcompcall('getAdminComponent',array($_context,$field->collection));
	$oa->addInterestIn('object_created',new FunctionObject($field, 'addFromEvent'));
	$oa->addInterestIn('object_deleted',new FunctionObject($field, 'removeFromEvent'));
	return $oa;
}
//@#

#@defmdf &getFieldEditor[Component](&$field: DateField)
{
	return new DateInput($field->getValue());
}
//@#

#@defmdf &getFieldEditor[Component](&$field: DateTimeField)
{
	return new DateTimeInput($field->getValue());
}
//@#

#@defmdf &getFieldEditor[Component](&$field: BoolField)
{
	return new CheckBox($field);
}
//@#

?>