<?php

require_once 'PersistentObjectPresenter.class.php';

class PersistentObjectViewer extends PersistentObjectPresenter {
    function initialize(){
    	$obj =& $this->obj;
    	$this->factory =& new ViewerFactory;
		$class = getClass($obj);
		PermissionChecker::addComponent($this,
					new CommandLink(array('text' => 'Delete', 'proceedFunction' => new FunctionObject($this, 'deleteObject', array('object' => & $obj)))),
					new FunctionObject(User::logged(), 'hasPermissions', array(getClass($obj).'=>Delete', '*',getClass($obj).'=>*'))
					,'delete');
       	$this->addComponent(new ActionLink($this, 'cancel', 'cancel', $n=null), 'cancel');
       	PermissionChecker::addComponent($this,
					new CommandLink(array('text' => 'Edit', 'proceedFunction' => new FunctionObject($this, 'editObject', array('object' => & $obj)))),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Edit', '*',$class.'=>*'))
					,'edit');

    	parent::initialize();
    }

    function &addField(&$field){
		$fc =& new FieldValueComponent;
		$fieldComponent = & $this->factory->createFor($field);
		$fc->addComponent($fieldComponent, 'value');
		$user =& User::logged();
		$class = getClass($this->obj);
		if ($user->hasPermissions(array($class.'=>Edit'))) {
            $fc->addComponent(new CommandLink(array('text' => $field->displayString, 'proceedFunction' => new FunctionObject($this, 'editField', array('field' => &$field, 'fvc' => &$fc)))), 'fieldName');
        } else {
			$fc->addComponent(new Label($field->displayString), 'fieldName');
        }

		return $fc;
    }

    function editField($params) {
   	  	$field =& $params['field'];
   	  	$fvc =& $params['fvc'];
    	$field_editor =& new FieldEditor(array('field' => &$field));
    	$field_editor->registerCallback('field_edited', new FunctionObject($this, 'fieldEdited'));
    	$fvc->call($field_editor);
    }


    function editObject($params) {
		$obj =& $params['object'];
		$msg =& $params['msg'];
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
    	//$ec->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	if (!empty($msg)){
    		$ec->displayValidationErrors($msg);
    	}
    	$this->call($ec);
	}

    function fieldEdited(&$field) {
    	$this->objectEdited($this->obj);
    }

    function objectEdited(&$object) {
		$ok = $object->save();

		if (!$ok){
			$this->editObject(array('object'=> &$object, 'msg' =>array('version'=>new ValidationException(array('message'=>'This object has been modified by another user')))));
		}
	}

	function cancel() {
		$this->callback('refresh');
	}

	function deleteObject($params) {
		$fc =& $params['object'];
		$translator = translator;
		if (!$translator)
			$translator = 'EnglishTranslator';
		$translator =& new $translator;
		$msg = $translator->translate('Are you sure that you want to delete the object?');
		$this->call(new QuestionDialog($msg, array('on_yes' => new FunctionObject($this, 'deleteConfirmed', array('object' => &$fc)), 'on_no' => new FunctionObject($this, 'deleteRejected'))));
	}

	function deleteConfirmed($params, $fcparams) {
		$fc =& $fcparams['object'];
		$ok = $obj->delete();
		if (!$ok) {
			$this->call(new NotificationDialog('Error deleting object', array('on_accept' => new FunctionObject($this, 'warningAccepted')) , 'warning'));
		}
		else {
			$this->refresh();
		}
	}

	function deleteRejected() {

	}


}

?>