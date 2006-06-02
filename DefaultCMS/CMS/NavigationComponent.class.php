<?php
class NavigationComponent extends Component {
	var $col;
	var $fields;
	var $classN;

	function NavigationComponent(&$col, $fields=null, $callbacks=array()) {
		$this->col = & $col;
		$this->classN = $col->dataType;

		if ($fields==null){
			$obj = & new $col->dataType;
			$this->fields = & $obj->allIndexFields();
		} else {
			$this->fields = $fields;
		}

		parent :: Component($callbacks);
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
		$this->addComponent(new CompositeFormComponent, 'objs');
		/* Size */
		$this->size =& new ValueHolder($s = 0);
		$this->addComponent(new Text($this->size), 'realSize');
		$this->pageSize =& new ValueHolder($pz = 10);
		$this->addComponent(new Input($this->pageSize), 'pSize');
		$this->pageSize->onChangeSend('refresh', $this);
		foreach ($this->fields as $f) {
			$fc = & new CompositeFormComponent;
			$fc->addComponent(new ActionLink($this, 'sort', $f->displayString, $f->colName));
			$this->addComponent($fc, $f->colName);
		}
		$this->refresh();
	}


	function refresh (){
		$col =&$this->col;
		$col->limit = $this->pageSize->getValue();
		$col->offset = $this->firstElement->getValue()-1;
		$this->size->setValue($col->size());
		$this->objs->deleteChildren();
		$col->refresh();
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