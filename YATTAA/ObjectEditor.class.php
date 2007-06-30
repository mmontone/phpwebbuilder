<?php

  /* Options:
   "display_buttons: when true, display save and cancel buttons. Default: true.
   "commit": when true, commit changes to the database. Default: true.
   "confirm_cancel": when true, confirm cancel. Default: true.
   "inform_success": when true, inform successful edition. Default: true.
   Mixins:
   DontDisplayButtons, DontCommit, DontConfirmCancel, DontInformSuccess
  */


class ObjectEditor extends ObjectPresenter {
	var $object_edited = false;
	var $edit_function;

	function initialize(){
	  $this->beginMemoryTransaction();
	  parent::initialize();
	  $this->addButtons();
	}

	function createInstance($params) {
	   $this->edit_function =& new LambdaObject('&$x', '');
	}

	function onEditionDo(&$function) {
	  $this->edit_function =& $function;
	}

	function getDefaultOptions() {
	  return array('display_buttons' => true, 'commit' => true, 'confirm_cancel' => true, 'inform_success' => true);
	}

 	function nonCallStop() {
 		if (!$this->object_edited) $this->doFlush();
 	}

	function doFlush(){
	  $this->rollbackMemoryTransaction();
	}

	function cancel() {
    	if (!$this->memory_transaction->isEmpty()) {
    		$this->confirmCancel();
    	}
    	else
    		$this->cancelConfirmed();
    }
	function &chooseFieldDisplayer(&$field){
		return mdcompcall('getFieldEditor', array(&$this,&$field));
	}
    function confirmCancel() {
      if ($this->options['confirm_cancel']) {
	$question =& QuestionDialog::Create($this->confirmCancelMessage());
    	$question->registerCallback('on_yes', new FunctionObject($this, 'cancelConfirmed'));
    	$question->registerCallback('on_no', new FunctionObject($this, 'cancelRejected'));
    	$this->call($question);
      }
      else {
	$this->cancelConfirmed();
      }
    }

    function confirmCancelMessage() {
    	//return '¿Descartar los cambios realizados?';
    	return Translator::Translate('Cancel all changes?');
    }

    function cancelRejected() {
    }

    function cancelConfirmed() {
    	$this->callback($this->cancelCallback());
    }

    function cancelCallback() {
    	return 'cancel';
    }

    function &getSaveLink($text='Save') {
    	$sl =& new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'saveObject')));
    	return $sl;
    }

    function &getCancelLink($text='Cancel') {
      $cl =& new CommandLink(array('text' => $text, 'proceedFunction' => new FunctionObject($this, 'cancel')));
      return $cl;
    }

	function addButtons() {
	  if ($this->options['display_buttons']) {
	    $this->addSaveButton();
	    $this->addCancelButton();
	  }
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

	#@php5
    function saveObject() {
	  // CommitInTransaction and unregisterAllObject should be threaded. That means,
	  // in the case of unregisterAllObject, only objects related to the creator component are unregistered.
	  //                                  -- marian

	  // TODO: fix mixins so I can put comments inside them :)
      try {
		$this->validateObject();
		$this->edit_function->callWith($this->object);
		$this->commitTransaction();
		$this->objectEdited();
      }
      catch (PWBValidationError $e) {
        	$this->addComponent(new ValidationErrorsDisplayer($this->object->validation_errors), 'validation_errors');
      }
	  catch (DBError $e) {
	  	$this->rollbackTransaction();
	  	$dialog =& ErrorDialog::Create($e->getMessage());
	  	$dialog->onAccept(new FunctionObject($this, 'doNothing'));
	  	$this->call($dialog);
	  }
    }
	//@#

	#@php4
    function saveObject() {
	  // CommitInTransaction and unregisterAllObject should be threaded. That means,
	  // in the case of unregisterAllObject, only objects related to the creator component are unregistered.
	  //                                  -- marian

	  // TODO: fix mixins so I can put comments inside them :)
		if (is_exception($e =& $this->validateObject())){
			$this->addComponent(new ValidationErrorsDisplayer($this->object->validation_errors), 'validation_errors');
		} else {
			$this->edit_function->callWith($this->object);
			if (is_exception($e =& $this->commitTransaction())){
			  	$this->rollbackTransaction();
			  	$dialog =& ErrorDialog::Create($e->getMessage());
			  	$dialog->onAccept(new FunctionObject($this, 'doNothing'));
			  	$this->call($dialog);
			} else {
				$this->objectEdited();
			}
      }
    }
	//@#

    function rollbackTransaction() {
      // We don't have to unregister all objects as in the case of ObjectCreators
    }

    function validateObject() {
    	return $this->object->validateAll();
    }

    function objectEditedCallback() {
    	return 'object_edited';
    }

    function objectEdited() {
      if ($this->options['inform_success']) {
	$dialog =& NotificationDialog::Create($this->successfulEditionMessage());
	$dialog->onAccept(new FunctionObject($this, 'successMessageConfirmed'));
	$this->call($dialog);
      }
      else {
	$this->successMessageConfirmed();
      }
    }

    function successfulEditionMessage() {
      return 'El objeto ha sido editado con éxito';
    }

    function successMessageConfirmed() {
      $this->object_edited = true;
      $this->callbackWith($this->objectEditedCallback(), $this->object);
    }
    function commitTransaction() {
      if ($this->options['commit']) {
	$this->commitMemoryTransaction();
      }
    }
}


#@defmdf &getFieldEditor[Component](&$field: DataField)
{
	$in =& new Input($field);
	return $in;
}
//@#

#@defmdf &getFieldEditor[Component](&$field: TextArea)
{
	$tac =& new TextAreaComponent($field);
	return $tac;
}
//@#


#@defmdf &getFieldEditor[Component](&$field: IndexField)
{
	$sel =& new Select($field->asValueModel(), new PersistentCollection($field->getDataType()));
	return $sel;
}
//@#

#@defmdf &getFieldEditor[Component](&$field: CollectionField)
{
	//$oa =& mdcompcall('getAdminComponent',array($_context,$field->getCollection()));
	//$oa->addInterestIn('object_created',new FunctionObject($field, 'addFromEvent'));
	//$oa->addInterestIn('object_deleted',new FunctionObject($field, 'removeFromEvent'));
	//return $oa;
	$lab =& new Label('');
	return $lab;
}
//@#

#@defmdf &getFieldEditor[Component](&$field: DateField)
{
	$di =& new DateInput($field->getValue());
	return $di;
}
//@#

#@defmdf &getFieldEditor[Component](&$field: DateTimeField)
{
	$di =& new DateTimeInput($field->getValue());
	return $di;
}
//@#

#@defmdf &getFieldEditor[Component](&$field: BoolField)
{
	$cb =& new CheckBox($field);
	return $cb;
}
//@#

?>