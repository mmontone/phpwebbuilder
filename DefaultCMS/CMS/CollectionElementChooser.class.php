<?php

class CollectionElementChooser extends CollectionNavigator {

	function CollectionElementChooser(&$col) {
		parent::CollectionNavigator($col);
	}

	function initialize() {
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		parent::initialize();
	}

	function newObject(&$n) {
		$obj = & new $this->classN;
		$ec =& new PersistentObjectEditor($obj);
    	$ec->registerCallback('refresh', callback($this, 'refresh'));
    	$ec->registerCallback('object_edited', new FunctionObject($this,'objectEdited'));
		$this->call($ec);
	}

	function objectEdited(&$object) {
		$object->save();
		$this->refresh();
	}

	function addLine(&$obj) {
		$fc = & new PersistentObjectViewer($obj, $this->fields);
		$this->objs->addComponent($fc);
		$fc->addComponent(new ActionLink($this, 'selectObject', 'Select', $obj), 'select');
	}

	function selectObject(&$obj){
		$this->callbackWith('selected', $obj);
	}
}
?>