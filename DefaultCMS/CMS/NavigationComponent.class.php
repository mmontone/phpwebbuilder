<?php
class NavigationComponent extends Component {
	var $col;
	var $classN;
	var $fields;
	function NavigationComponent(&$col, $fields=null) {
		$this->col = & $col;
		$this->classN = $col->dataType;
		if ($fields==null){
			$c = $this->classN;
			$obj = & new $c;
			$this->fields = & $obj->allIndexFieldNames();
		} else {
			$this->fields = $fields;
		}
		parent :: Component();
	}
	function initialize() {
		$this->addComponent(new ActionLink($this, 'nextPage', 'next', $n = null), 'next');
		$this->addComponent(new ActionLink($this, 'prevPage', 'prev', $n = null), 'prev');
		$this->addComponent(new ActionLink($this, 'firstPage', 'first', $n = null), 'first');
		$this->addComponent(new ActionLink($this, 'lastPage', 'last', $n = null), 'last');
		$this->addComponent(new ActionLink($this, 'filter', 'filter', $n = null), 'filter');
		$this->addComponent(new ActionLink($this, 'getValue', 'refresh', $n = null), 'refresh');
		$this->firstElement =& new ValueHolder($fp = 1);
		$this->firstElement->onChangeSend('refresh', $this);
		$this->addComponent(new Input($this->firstElement), 'firstElem');
		$this->addComponent(new FormComponent($v=null), 'objs');
		/* Size */
		$this->size =& new ValueHolder($s = 0);
		$this->addComponent(new Text($this->size), 'realSize');
		$this->pageSize =& new ValueHolder($pz = 10);
		$this->addComponent(new Input($this->pageSize), 'pSize');
		$this->pageSize->onChangeSend('refresh', $this);
		$c = $this->classN;
		foreach ($this->fields as $f) {
			$fc = & new FormComponent($null=null);
			$obj = & new $this->classN;
			$fc->addComponent(new ActionLink($this, 'sort', $obj->$f->displayString, $f));
			$this->addComponent($fc, $f);
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
	function filter(){
		$fc =& new FilterCollectionComponent($this->col);
		$fc->registerCallbacks(array('done'=>callback($this, 'refresh')));
		$this->call($fc);
	}
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
?>