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
    	$ec->registerCallbacks(array('refresh'=>callback($this, 'refresh')));
		$this->call($ec);
	}
	function newObject(&$n) {
		$obj = & new $this->classN;
		$this->editObject($obj);
	}
	function deleteObject(&$fc) {
		$fc->obj->delete();
		$this->refresh();
	}
	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj);
		$this->objs->addComponent($fc);
		$fc->addComponent(new ActionLink($this, 'editObject', 'Edit', $obj), 'edit');
		$fc->addComponent(new ActionLink($this, 'deleteObject', 'Delete', $fc), 'delete');
	}
}
?>