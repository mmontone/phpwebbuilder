<?php

require_once 'NavigationComponent.class.php';

class SelectCollectionComponent extends NavigationComponent {
	function initialize() {
		$class = & $this->classN;
		$this->addComponent(new Text(new ValueHolder($class)), 'className');
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		parent::initialize();
	}
	function newObject(&$n) {
		$obj = & new $this->classN;
		$ec =& new EditObjectComponent($obj);
    	$ec->registerCallbacks(array('refresh'=>new FunctionObject($this, 'refresh')));
		$this->call($ec);
	}
	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj);
		$this->objs->addComponent($fc);
		$fc->addComponent(new ActionLink($this, 'selectObject', 'Select', $obj), 'select');
	}
	function selectObject(&$obj){
		$this->callbackWith('selected', $obj);
	}
}
?>