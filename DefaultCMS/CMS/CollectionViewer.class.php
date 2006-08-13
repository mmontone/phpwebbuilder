<?php

class CollectionViewer extends CollectionNavigator {
	function initialize() {
		$class = & $this->classN;
		$this->addComponent(new Label($class), 'className');
		$u =& User::logged();
		$this->addComponent(new ActionLink($this, 'newObject', 'New', $n = null),'new');
		parent::initialize();
	}
	function viewObject($params) {
		$obj =& $params['object'];
		$viewer =& new PersistentObjectViewer($obj);
    	$viewer->registerCallback('refresh', new FunctionObject($this, 'refresh'));
    	$viewer->registerCallback('object_deleted', new FunctionObject($this, 'objectDeleted'));
    	$this->call($viewer);
	}
	function checkNewObjectPermissions($params){
		$u =&User::logged();
		return $u->hasPermissions(array($this->col->getDataType().'=>Add', '*',$this->col->getDataType().'=>*'));
	}
	function newObject(&$n) {
		$dt = $this->col->getDataType();
		$obj = & new $dt;
		$c =& $this->col->conditions;
		foreach($c as $f=>$cond){
			$obj->$f->setValue($cond[1]);
		}
		$obj->commitChanges();
		$this->viewObject(array('object' => &$obj));
		$this->holder->component->editObject(array('object' => &$obj));
	}
	function objectDeleted($fc) {
		$this->refresh();
	}
	function &addLine(&$obj) {
		$fc = & new PersistentObjectViewer($obj, $this->fields);
		$this->objs->addComponent($fc);
		$fc->addComponent(new CommandLink(array('text' => 'View', 'proceedFunction' => new FunctionObject($this, 'viewObject', array('object' => & $obj)))),'viewer');
		return $fc;
	}
}
?>