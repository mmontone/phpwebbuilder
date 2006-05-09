<?php
class ShowCollectionComponent extends Component {
	var $col;
	var $classN;
	var $params;
	function ShowCollectionComponent(&$colclass) {
		if (is_object($colclass)) {
			$this->col = & $colclass;
			$this->classN = $colclass->dataType;
			$this->params = array();
		} else
			if (is_array($colclass)) {
				$this->col = & new PersistentCollection($colclass["ObjType"]);
				$this->classN = $colclass["ObjType"];
				$this->params =& $colclass;
			} else {
				$this->col = & new PersistentCollection($colclass);
				$this->classM = $colclass;
				$this->params = array();
			}
		parent :: Component();
	}
	function initialize() {
		$class = & $this->classN;
		$this->add_component(new Text(new ValueHolder($class)), 'className');
		$this->add_component(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		$this->col->order = $this->params["order"];
		$this->col->conditions = $this->params["conditions"];
		$obj = & new $class;
		$fs = & $obj->indexFields;
		foreach ($fs as $f) {
			$fc = & new Obj;
			$fc->add_component(new Text(new ValueHolder($fs[$f])));
			$this->add_component($fc);
		}
		$objects = & $this->col->objects();
		$ks = & array_keys($objects);
		foreach ($ks as $k) {
			$this->addLine($objects[$k]);
		}
	}
	function editObject(&$obj) {
		$this->call(new EditObjectComponent($obj));
	}
	function newObject(&$n) {
		$obj = & new $this->classN;
		$this->addLine($obj);
		$this->editObject($obj);
	}
	function deleteObject(&$fc) {
		$fc->obj->delete();
		$fc->delete();
	}
	function addLine(&$obj) {
		$fc = & new ShowObjectComponent($obj);
		$this->add_component($fc);
		$fc->add_component(new ActionLink($this, 'editObject', 'Edit', $obj), 'edit');
		$fc->add_component(new ActionLink($this, 'deleteObject', 'Delete', $fc), 'delete');
	}
	function getValue(){}
}

class Obj extends Component {
}
?>