<?php

require_once 'NavigationComponent.class.php';

class ShowCollectionComponent extends NavigationComponent {
	function initialize() {
		$class = & $this->classN;
		$this->addComponent(new Text(new ValueHolder($class)), 'className');
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
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
		$fc->obj->delete();
		$this->refresh();
	}
	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj, $this->fields);
		$this->objs->addComponent($fc);
		$fc->addComponent(new ActionLink($this, 'editObject', 'Edit', $obj), 'edit');
		$fc->addComponent(new ActionLink($this, 'deleteObject', 'Delete', $fc), 'delete');
	}
}
?>