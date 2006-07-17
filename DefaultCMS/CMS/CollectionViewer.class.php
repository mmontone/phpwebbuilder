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

	function newObject(&$n) {
		$obj = & new $this->classN;
		$c =& $this->col->conditions;
		foreach($c as $f=>$cond){
			$obj->$f->setValue($cond[1]);

		}
		$obj->commitChanges();
		$this->editObject(array('object' => &$obj));
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

	function warningAccepted() {

	}


	function deleteRejected() {

	}

	function &addLine(&$obj) {
		$fc = & new PersistentObjectViewer($obj, $this->fields);
		$class = & $this->classN;
		$u =& User::logged();

		/*
		PermissionChecker::addComponent($fc,
			new CommandLink(array('text' => 'View', 'proceedFunction' => new FunctionObject($this, 'viewObject', array('object' => & $obj)))),
			new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Show', '*',$class.'=>*')),
			'viewer');*/
		$fc->addComponent(new CommandLink(array('text' => 'View', 'proceedFunction' => new FunctionObject($this, 'viewObject', array('object' => & $obj)))),'viewer');

		PermissionChecker::addComponent($fc,
					new CommandLink(array('text' => 'Edit', 'proceedFunction' => new FunctionObject($this, 'editObject', array('object' => & $obj)))),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Edit', '*',$class.'=>*'))
					,'editor');
		PermissionChecker::addComponent($fc,
					new CommandLink(array('text' => 'Delete', 'proceedFunction' => new FunctionObject($this, 'deleteObject', array('object' => & $fc)))),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Delete', '*',$class.'=>*'))
					,'deleter');
		return $fc;
	}
}

class DeleterAspect {
	function deleteObject(&$self, $params) {
		$fc =& $params['object'];
		$translator = translator;
		if (!$translator)
			$translator = 'EnglishTranslator';
		$translator =& new $translator;
		$msg = $translator->translate('Are you sure that you want to delete the object?');
		$self->call(new QuestionDialog($msg, array('on_yes' => new FunctionObject($self, 'deleteConfirmed', array('object' => &$fc)), 'on_no' => new FunctionObject($self, 'deleteRejected'))));
	}

	function deleteConfirmed(&$self, $params, $fcparams) {
		$fc =& $fcparams['object'];
		$ok = $fc->obj->delete();
		if (!$ok) {
			$self->call(new NotificationDialog('Error deleting object', array('on_accept' => new FunctionObject($self, 'warningAccepted')) , 'warning'));
		}
		else {
			$self->refresh();
		}
	}
}

class EditorAspect {
	function editObject(&$self, $params) {
		$obj =& $params['object'];
		$msg =& $params['msg'];
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('cancel', new FunctionObject($self, 'cancel'));
    	$ec->registerCallback('object_edited', new FunctionObject($self, 'objectEdited'));
    	$ec->registerCallback('refresh', new FunctionObject($self, 'refresh'));
    	if (!empty($msg)){
    		$ec->displayValidationErrors($msg);
    	}
    	$self->call($ec);
	}

	function objectEdited(&$self, &$object) {
		$ok = $object->save();
		if ($ok){
			$self->refresh();
		} else {
			$self->editObject(array('object'=> &$object, 'msg' =>array('version'=>new ValidationException(array('message'=>'This object has been modified by another user')))));
		}
	}

	function cancel(&$self) {
		//$self->refresh();
	}
}

//class CollectionElement extends Component {}
?>