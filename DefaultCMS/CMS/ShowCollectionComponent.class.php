<?php

require_once 'NavigationComponent.class.php';

class ShowCollectionComponent extends NavigationComponent {
	function initialize() {
		$class = & $this->classN;
		$this->add_component(new Text(new ValueHolder($class)), 'className');
		$this->add_component(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
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
		$this->objs->add_component($fc);
		$fc->add_component(new ActionLink($this, 'editObject', 'Edit', $obj), 'edit');
		$fc->add_component(new ActionLink($this, 'deleteObject', 'Delete', $fc), 'delete');
	}
}
?>