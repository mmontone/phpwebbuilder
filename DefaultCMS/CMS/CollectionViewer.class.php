<?php

require_once 'CollectionNavigator.class.php';

class CollectionViewer extends CollectionNavigator {
	function initialize() {
		$class = & $this->classN;
		$this->addComponent(new Label($class), 'className');
		$u =& User::logged();
		PermissionChecker::addComponent($this,
					new ActionLink($this, 'newObject', 'New', $n = null),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Add', '*',$class.'=>*'))
					,'new');
		parent::initialize();
	}
	/* Editing */
	function editObject(&$obj, $msg=array()) {
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('cancel', new FunctionObject($this, 'cancel'));
    	$ec->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
    	$ec->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	if (!empty($msg)){
    		$ec->displayValidationErrors($msg);
    	}
    	$this->call($ec);
	}

	function viewObject(&$obj) {
		$viewer =& new PersistentObjectViewer($obj);
    	$viewer->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	$this->call($viewer);
	}

	function objectEdited(&$object) {
		$ok = $object->save();
		if ($ok){
			$this->refresh();
		} else {
			$this->editObject($object, array('save'=>DB::lastError() . DB::lastSQL()));
		}
	}

	function cancel() {
		//$this->refresh();
	}

	function newObject(&$n) {
		$obj = & new $this->classN;
		$c =& $this->col->conditions;
		foreach($c as $f=>$cond){
			$obj->$f->setValue($cond[1]);

		}
		$this->editObject($obj);
	}
	function deleteObject(&$fc) {
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
		if (!$ok)
			echo 'Error deleting object';
		$this->refresh();
	}

	function deleteRejected() {

	}

	function addLine(&$obj) {
		$fc = & new PersistentObjectViewer($obj, $this->fields);
		$class = & $this->classN;
		$this->objs->addComponent($fc);
		$u =& User::logged();
		//$fc->addComponent(new ActionLink2(array('action'=>new FunctionObject($this, 'viewObject'), 'text'=>'View')), 'view');
		PermissionChecker::addComponent($fc,
					new ActionLink($this, 'editObject', 'Edit', $obj),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Edit', '*',$class.'=>*'))
					,'edit');
		PermissionChecker::addComponent($fc,
					new ActionLink($this, 'deleteObject', 'Delete', $fc),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Delete', '*',$class.'=>*'))
					,'delete');
	}
}
?>