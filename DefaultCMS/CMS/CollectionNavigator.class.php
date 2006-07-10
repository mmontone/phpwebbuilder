<?php
class CollectionNavigator extends Component {
	var $col;
	var $fields;
	var $classN;

	function CollectionNavigator(&$col, $fields=null, $callbacks=array()) {
		$this->col = & $col;
		$this->col->addEventListener(array('changed'=>'refresh'), $this);
		$this->classN = $col->getDataType();

		if ($fields==null){
			$obj = & new $this->classN;
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
		$this->addComponent(new ActionLink($this, 'refresh', 'refresh', $n = null), 'refresh');
		$this->firstElement =& new ValueHolder($fp = 1);
		$this->firstElement->onChangeSend('refresh', $this);
		$this->addComponent(new Input($this->firstElement), 'firstElem');
		//$this->firstElem->addEventListener(array('change'=>'refresh'), $this);
		$this->addComponent(new CompositeWidget, 'objs');
		/* Size */
		$this->size =& new ValueHolder($s = 0);
		$this->addComponent(new Text($this->size), 'realSize');
		$this->pageSize =& new ValueHolder($pz = 10);
		$this->pageSize->onChangeSend('refresh', $this);
		$this->addComponent(new Input($this->pageSize), 'pSize');
		//$this->pSize->addEventListener(array('change'=>'refresh'), $this);
		foreach ($this->fields as $f) {
			$fc = & new CompositeWidget;
			$this->addComponent($fc, $f->colName);
			$fc->addComponent(new ActionLink($this, 'sort', $f->displayString, $f->colName));
		}
		$this->refresh();
	}


	function refresh (){
		$col =&$this->col;
		$col->limit = $this->pageSize->getValue();
		$col->offset = $this->firstElement->getValue()-1;
		$this->size->setValue($col->size());
		$this->objs->deleteChildren();
		// The next line is only necessary if elements are not added or removed
		// through the collection interface (the in-memory collection doesn't get notified)
		$col->refresh();
		$elements = & $col->elements();
		$ks = array_keys($elements);
		foreach ($ks as $k) {
			$this->addLine($elements[$k]);
		}
		$this->view->redraw();
	}
	function getValue(){}
	/* Navigation */
	function filter(){
		$fc =& new CollectionFilterer($this->col);
		$fc->registerCallbacks(array('done'=>callback($this, 'refresh')));
		$this->call($fc);
	}
	function setStartValue($val){
		$last = $this->col->size()-$this->pageSize->getValue();
		$this->firstElement->setValue($r = max(min($val,$last), 1));
	}
	function prevPage(){
		$this->setStartValue($this->firstElement->getValue()-$this->pageSize->getValue());
	}
	function nextPage(){
		$this->setStartValue($this->firstElement->getValue()+$this->pageSize->getValue());
	}
	function firstPage(){
		$this->setStartValue($r = 1);
	}
	function lastPage(){
		$this->setStartValue($this->col->size());
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