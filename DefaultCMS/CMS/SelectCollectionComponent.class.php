<?php

require_once 'NavigationComponent.class.php';

class SelectCollectionComponent extends NavigationComponent {

	function SelectCollectionComponent(&$col) {
		parent::NavigationComponent($col);
	}

	function initialize() {
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		parent::initialize();
	}

	function newObject(&$n) {
		$obj = & new $this->classN;
		$ec =& new EditObjectComponent($obj);
    	$ec->registerCallback('refresh', callback($this, 'refresh'));
		$this->call($ec);
	}

	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj, $this->fields);
		$this->objs->addComponent($fc);
		$fc->addComponent(new ActionLink($this, 'selectObject', 'Select', $obj), 'select');
	}

	function selectObject(&$obj){
		$this->callbackWith('selected', $obj);
	}
}
?>