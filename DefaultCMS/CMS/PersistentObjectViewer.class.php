<?php

class PersistentObjectViewer extends PersistentObjectPresenter {
    function initialize(){
    	$obj =& $this->obj;
    	$this->factory =& new ViewerFactory;
		$class = getClass($obj);
		$this->addComponent(new CommandLink(array('text' => 'Delete', 'proceedFunction' => new FunctionObject($this, 'deleteObject', array('object' => & $obj)))),'deleter');
       	$this->addComponent(new ActionLink($this, 'cancel', 'cancel', $n=null), 'cancel');
       	$this->addComponent(new CommandLink(array('text' => 'Edit', 'proceedFunction' => new FunctionObject($this, 'editObject', array('object' => & $obj)))),'editor');
    	parent::initialize();
    }

    function &addField(&$field){
		$fc =& new FieldValueComponent;
		$fieldComponent = & $this->factory->createFor($field);
		$fc->addComponent($fieldComponent, 'value');
		if ($this->checkEditObjectPermissions(array('object'=> &$this->obj))) {
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

	function checkEditObjectPermissions($params) {
		$u =& User::logged();
		return $u->hasPermissions(array(getClass($params['object']).'=>Edit', '*',getClass($params['object']).'=>*'));
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

	function checkDeleteObjectPermissions($params) {
		$u =& User::logged();
		return $u->hasPermissions(array(getClass($params['object']).'=>Delete', '*',getClass($params['object']).'=>*'));
	}

	function deleteObject($params) {
		$obj =& $params['object'];
		$translator = translator;
		if (!$translator)
			$translator = 'EnglishTranslator';
		$translator =& new $translator;
		$msg = $translator->translate('Are you sure that you want to delete the object?');
		$this->call(new QuestionDialog($msg, array('on_yes' => new FunctionObject($this, 'deleteConfirmed', array('object' => &$obj)), 'on_no' => new FunctionObject($this, 'deleteRejected'))));
	}

	function deleteConfirmed($params, $objparams) {
		$obj =& $objparams['object'];
		$ok = $obj->delete();
		if (!$ok) {
			$this->call(new NotificationDialog('Error deleting object', array('on_accept' => new FunctionObject($this, 'warningAccepted')) , 'warning'));
		} else {
			$this->callback('object_deleted');
		}
	}

	function deleteRejected() {

	}


	function warningAccepted() {

	}
}

?>