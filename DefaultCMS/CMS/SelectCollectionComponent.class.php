<?php

require_once 'NavigationComponent.class.php';

class SelectCollectionComponent extends NavigationComponent {
	function initialize() {
		$class = & $this->classN;
		$this->add_component(new Text(new ValueHolder($class)), 'className');
		$this->add_component(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		parent::initialize();
	}
	function newObject(&$n) {
		$obj = & new $this->classN;
		$ec =& new EditObjectComponent($obj);
    	$ec->registerCallbacks(array('refresh'=>callback($this, 'refresh')));
		$this->call($ec);
	}
	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj);
		$this->objs->add_component($fc);
		$fc->add_component(new ActionLink($this, 'selectObject', 'Select', $obj), 'select');
	}
	function selectObject(&$obj){
		$this->callback('selected', array('object'=>&$obj));
	}
}
?>