<?php

require_once 'NavigationComponent.class.php';

class ShowCollectionComponent extends NavigationComponent {
	function initialize() {
		$class = & $this->classN;
		$this->addComponent(new Text(new ValueHolder($class)), 'className');
		$u =& User::logged();
		PermissionChecker::addComponent($this,
					new ActionLink($this, 'newObject', 'New', $n = null),
					new FunctionObject(User::logged(), 'hasPermissions', array($class.'=>Add', '*',$class.'=>*'))
					,'new');
		parent::initialize();
	}
	/* Editing */
	function editObject(&$obj) {
		$ec =& new EditObjectComponent($obj);
    	$ec->registerCallback('cancel', new FunctionObject($this, 'cancel'));
    	$ec->registerCallback('object_edited', new FunctionObject($this, 'objectEdited'));
    	$ec->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	$this->call($ec);
	}

	function objectEdited(&$object) {
		$object->save();
		$this->refresh();
	}

	function cancel() {
		$this->refresh();
	}

	function newObject(&$n) {
		$obj = & new $this->classN;
		$c =& $this->col->conditions;
		foreach($c as $f=>$cond){
			$obj->$f->value = $cond[1];
		}
		$this->editObject($obj);
	}
	function deleteObject(&$fc) {
		$this->call(new QuestionDialog('Are you sure that you want to delete the object?', array('on_yes' => callback($this, 'deleteConfirmed')), $fc));
	}

	function deleteConfirmed(&$fc) {
		$ok = $fc->obj->delete();
		$this->refresh();
	}

	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj, $this->fields);
		$class = & $this->classN;
		$this->objs->addComponent($fc);
		$u =& User::logged();
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