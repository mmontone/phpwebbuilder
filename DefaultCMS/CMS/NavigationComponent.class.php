<?php
class NavigationComponent extends Component {
	var $col;
	var $classN;
	var $params;
	function NavigationComponent(&$colclass) {
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
		/* Navigation */
		$this->add_component(new ActionLink($this, 'nextPage', 'next', $n = null), 'next');
		$this->add_component(new ActionLink($this, 'prevPage', 'prev', $n = null), 'prev');
		$this->add_component(new ActionLink($this, 'firstPage', 'first', $n = null), 'first');
		$this->add_component(new ActionLink($this, 'lastPage', 'last', $n = null), 'last');
		$this->add_component(new ActionLink($this, 'getValue', 'refresh', $n = null), 'refresh');
		$this->firstElement =& new ValueHolder($fp = 1);
		$this->firstElement->onChangeSend('refresh', $this);
		$this->add_component(new Input($this->firstElement), 'firstElem');
		$this->add_component(new FormComponent($v=null), 'objs');
		/* Size */
		$this->size =& new ValueHolder($s = 0);
		$this->add_component(new Text($this->size), 'realSize');
		$this->pageSize =& new ValueHolder($pz = 10);
		$this->add_component(new Input($this->pageSize), 'pSize');
		$this->pageSize->onChangeSend('refresh', $this);
		$c = $this->classN;
		$obj = & new $c;
		$fs = & $obj->allIndexFields();
		foreach ($fs as $f) {
			$fc = & new Obj;
			$fc->add_component(new ActionLink($this, 'sort', $f->displayString, $f->displayString));
			$this->add_component($fc);
		}
		$this->refresh();
	}
	function refresh (){
		$col =&$this->col;
		$col->limit = $this->pageSize->getValue();
		$col->offset = $this->firstElement->getValue()-1;
		$this->size->setValue($col->size());
		$this->objs->deleteChildren();
		$objects = & $col->objects();
		$ks = array_keys($objects);
		foreach ($ks as $k) {
			$this->addLine($objects[$k]);
		}
	}
	function getValue(){}
	/* Navigation */
	function prevPage(){
		$this->firstElement->setValue($r = max($this->firstElement->getValue()-$this->pageSize->getValue(), 1));
	}
	function nextPage(){
		$this->firstElement->setValue($r = max(1,min($this->firstElement->getValue()+$this->pageSize->getValue(), $this->col->size()-$this->pageSize->getValue()+1)));
	}
	function firstPage(){
		$this->firstElement->setValue($r = 1);
	}
	function lastPage(){
		$this->firstElement->setValue($r = $this->col->size()-$this->pageSize->getValue()+1);
	}
	function sort($fname){
		if ($this->colorder == $fname){
			$this->col->order = " ORDER BY ".$fname . " DESC ";
			$this->colorder = "";
		} else {
			$this->colorder = $fname;
			$this->col->order = " ORDER BY ".$fname;
		}
		$this->refresh();
	}
}

class Obj extends Component {
}
?>