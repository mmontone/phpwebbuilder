<?php

require_once 'CollectionNavigator.class.php';

class CollectionViewer extends CollectionNavigator {
	function initialize() {
		$class = & $this->classN;
		$this->addComponent(new Label($class), 'className');
		$u =& User::logged();
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null),'new');
		parent::initialize();
	}
	/* Editing */
	function checkEditObjectPermissions($params){
		$u =&User::logged();
		return $u->hasPermissions(array(getClass($params['object']).'=>Edit', '*',getClass($params['object']).'=>*'));
	}
	function editObject($params) {
		$obj =& $params['object'];
		$msg =& $params['msg'];
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('cancel', new FunctionObject($this, 'cancel'));
    	$ec->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
    	$ec->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	if (!empty($msg)){
    		$ec->displayValidationErrors($msg);
    	}
    	$this->call($ec);
	}

	function viewObject($params) {
		$obj =& $params['object'];
		$viewer =& new PersistentObjectViewer($obj);
    	$viewer->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	$this->call($viewer);
	}

	function objectEdited(&$object) {
		$ok = $object->save();
		if ($ok){
			$this->refresh();
		} else {
			$this->editObject(array('object'=> &$object, 'msg' =>array('version'=>new ValidationException(array('message'=>'This object has been modified by another user')))));
		}
	}

	function cancel() {
		//$this->refresh();
	}
	function checkNewObjectPermissions($params){
		$u =&User::logged();
		return $u->hasPermissions(array(getClass($params['object']).'=>Add', '*',getClass($params['object']).'=>*'));
	}
	function newObject(&$n) {
		$obj = & new $this->classN;
		$c =& $this->col->conditions;
		foreach($c as $f=>$cond){
			$obj->$f->setValue($cond[1]);

		}
		$obj->commitChanges();
		$this->editObject(array('object' => &$obj));
	}
	function checkDeleteObjectPermissions($params){
		$u =&User::logged();
		return $u->hasPermissions(array(getClass($params['object']).'=>Delete', '*',getClass($params['object']).'=>*'));
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
		$ok = $fc->obj->delete();
		if (!$ok) {
			$this->call(new NotificationDialog('Error deleting object', array('on_accept' => new FunctionObject($this, 'warningAccepted')) , 'warning'));
		}
		else {
			$this->refresh();
		}
	}
	function warningAccepted() {}
	function deleteRejected() {}
	function &addLine(&$obj) {
		$fc = & new PersistentObjectViewer($obj, $this->fields);
		$this->objs->addComponent($fc);
		$fc->addComponent(new CommandLink(array('text' => 'View', 'proceedFunction' => new FunctionObject($this, 'viewObject', array('object' => & $obj)))),'viewer');
		$fc->addComponent(new CommandLink(array('text' => 'Edit', 'proceedFunction' => new FunctionObject($this, 'editObject', array('object' => & $obj)))),'editor');
		$fc->addComponent(new CommandLink(array('text' => 'Delete', 'proceedFunction' => new FunctionObject($this, 'deleteObject', array('object' => & $fc)))),'deleter');
		return $fc;
	}
}
?>