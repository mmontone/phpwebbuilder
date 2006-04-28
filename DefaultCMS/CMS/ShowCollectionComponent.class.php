<?php
class ShowCollectionComponent extends Component {
	var $col;
	var $classN;
	function ShowCollectionComponent(&$colclass) {
		if (is_object($colclass)) {
			$this->col = & $colclass;
			$this->classN = $colclass->dataType;
		} else
			if (is_array($colclass)) {
				$this->col = & new PersistentCollection($colclass["ObjType"]);
				$this->classN = $colclass["ObjType"];
			} else {
				$this->col = & new PersistentCollection($colclass);
				$this->classM = $colclass;
			}
		parent :: Component();
	}
	function initialize() {
		$objects = & $this->col->objects();
		$ks = & array_keys($objects);
		$class = & $this->classN;
		$this->add_component(new Text($class), 'className');
		$obj = & new $class;
		$fs = & $obj->indexFields;
		foreach ($fs as $f) {
			$fc = & new Obj;
			$fc->add_component(new Text($fs[$f]));
			$this->add_component($fc);
		}
		$this->add_component(new ActionLink($this, 'newObject', 'New', $n = null), 'new');
		foreach ($ks as $k) {
			$fc = & new ShowObjectComponent($objects[$k]);
			$this->add_component($fc);
			$fc->add_component(new ActionLink($this, 'editObject', 'Edit', $objects[$k]), 'edit');
			$fc->add_component(new ActionLink($this, 'deleteObject', 'Delete', $fc), 'delete');
		}
	}
	function editObject(&$obj) {
		$this->call(new EditObjectComponent($obj));
	}
	function newObject(&$n) {
		$obj = & new $this->classN;
		$fc = & new ShowObjectComponent($obj);
		$this->add_component($fc);
		$this->editObject($obj);
	}
	function deleteObject(&$fc) {
		$fc->obj->delete();
		$fc->delete();
	}
}

class Obj extends Component {
}
?>