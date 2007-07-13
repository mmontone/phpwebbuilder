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

    /*function &addField(&$field){
		$fc =& new FieldValueComponent;
		$fieldComponent = & $this->factory->createFor($field);
		$fc->addComponent($fieldComponent, 'value');
		if ($this->checkEditObjectPermissions(array('object'=> &$this->obj))) {
            $fc->addComponent(new CommandLink(array('text' => $field->displayString, 'proceedFunction' => new FunctionObject($this, 'editField', array('field' => &$field, 'fvc' => &$fc)))), 'fieldName');
        } else {
			$fc->addComponent(new Label($field->displayString), 'fieldName');
        }
		return $fc;
    }*/

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

    #@php4
    function objectEdited(&$object) {
		$db =& DBSession::Instance();
		$db->beginTransaction();
		$ex =& $db->save($object);
		if (is_exception($ex))
        {
		   	$db->rollback();
			$dialog =& ErrorDialog::Create($ex->getMessage());
			$dialog->onAccept(new FunctionObject($this, 'doNothing'));
			$this->call($dialog);
		}
		else
        {
			$db->commit();
			$object->commitChanges();
		}
	}//@#

    #@php5
    function objectEdited(&$object) {
        $db =& DBSession::Instance();
        $db->beginTransaction();
        try {
            $db->save($object);
            $db->commit();
            $object->commitChanges();
        }
        catch (Exception $ex) {
            $db->rollback();
            $dialog =& ErrorDialog::Create($ex->getMessage());
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
        }
    }//@#

	function doNothing() {

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
		$msg = Translator::translate('Are you sure that you want to delete the object?');
		$this->call($qd =& QuestionDialog::create($msg));
		$qd->registerCallbacks(array('on_yes' => new FunctionObject($this, 'deleteConfirmed', array('object' => &$obj)), 'on_no' => new FunctionObject($this, 'deleteRejected')));
	}

	#@php4
    function deleteConfirmed($params, $objparams) {
		$obj =& $objparams['object'];
		$db =& DBSession::Instance();
		$db->beginTransaction();
		$ex =& $db->delete($obj);
		if (is_exception($ex)) {
			$db->rollbackTransaction();
			$dialog =& NotificationDialog::Create('Error deleting object: ' . $ex->getMessage());
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
		} else {
			$db->commitTransaction();
			$this->callback('object_deleted');
		}
	}//@#

    #@php5
    function deleteConfirmed($params, $objparams) {
        $obj =& $objparams['object'];
        $db =& DBSession::Instance();
        $db->beginTransaction();
        try {
            $db->delete($obj);
            $db->commitTransaction();
            $this->callback('object_deleted');
        }
        catch (Exception $ex) {
            $db->rollbackTransaction();
            $dialog =& NotificationDialog::Create('Error deleting object: ' . $ex->getMessage());
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
        }
    }//@#


	function deleteRejected() {

	}


	function warningAccepted() {

	}
}

?>