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

		/* Navigation */
		$this->add_component(new ActionLink($this, 'nextPage', 'next', $n = null), 'next');
		$this->add_component(new ActionLink($this, 'prevPage', 'prev', $n = null), 'prev');
		$this->add_component(new ActionLink($this, 'firstPage', 'first', $n = null), 'first');
		$this->add_component(new ActionLink($this, 'lastPage', 'last', $n = null), 'last');
		$this->firstElement =& new ValueHolder($fp = 0);
		$this->add_component(new Input($this->firstElement), 'firstrElem');
		$this->add_component(new FormComponent($v=null), 'objs');
		/* Size */
		$this->size =& new ValueHolder($s = 0);
		$this->add_component(new Text($this->size), 'realSize');
		$this->pageSize =& new ValueHolder($pz = 0);
		$this->add_component(new Input($this->pageSize), 'pSize');

		$obj = & new $class;
		$fs = & $obj->indexFields;
		foreach ($fs as $f) {
			$fc = & new Obj;
			$fc->add_component(new ActionLink($this, 'sort', $fs[$f], $fs[$f]));
			$this->add_component($fc);
		}
		$this->refresh();
	}
	function refresh (){
		$col =&$this->col;
		$col->limit = $this->pageSize->getValue();
		$col->offset = $this->firstElement->getValue();
		$this->size->setValue($col->size());
		$objects = & $col->objects();
		$ks = array_keys($objects);
		$this->objs->deleteChildren();
		foreach ($ks as $k) {
			$this->addLine($objects[$k]);
		}
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
	function getValue(){}
	/* Navigation */
	function prevPage(){
		$this->firstElement->setValue($r = $this->firstElement->getValue()-$this->pageSize->getValue());
		$this->refresh();
	}
	function nextPage(){
		$this->firstElement->setValue($r = $this->firstElement->getValue()+$this->pageSize->getValue());
		$this->refresh();
	}
	function firstPage(){
		$this->firstElement->setValue($r = 0);
		$this->refresh();
	}
	function lastPage(){
		$this->firstElement->setValue($r = $this->col->size()-$this->pageSize->getValue());
		$this->refresh();
	}
}

class Obj extends Component {
}
?>