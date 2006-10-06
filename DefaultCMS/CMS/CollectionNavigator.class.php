<?php
class CollectionNavigator extends Component {
	var $col;
	var $fields;
	var $classN;

	function CollectionNavigator(&$col, $fields=null, $callbacks=array()) {
		if ($col == null) print_backtrace();
		$this->col = & $col;
		$this->col->addEventListener(array('changed'=>'refresh'), $this);
		$this->classN = $col->getDataType();

		if ($fields==null){
			$this->fields = & $col->allFields();
		} else {
			$this->fields =& $fields;
		}

		parent::Component();
		$this->registerCallbacks($callbacks);
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
		foreach ($this->fields as $n=>$f) {
			$fc = & new CompositeWidget;
			if (is_string($f)) {
				$this->addComponent($fc, $f);
				$fc->addComponent(new ActionLink($this, 'sort', $f, $n));
			} else {
				$this->addComponent($fc, $f->colName);
				$fc->addComponent(new ActionLink($this, 'sort', $f->displayString, $f->colName));
			}
		}
		$this->refresh();
	}

	function setPageSize($size) {
		$this->pageSize->setValue($size);
	}

	function refresh () {
		$col =&$this->col;
		$col->limit = $this->pageSize->getValue();
		$col->offset = $this->firstElement->getValue()-1;
		$this->size->setValue($col->size());
		$this->objs->deleteChildren();
		// The next line is only necessary if elements are not added or removed
		// through the collection interface (the in-memory collection doesn't get notified)
		$col->refresh();
		$elements = & $col->elements();
		if (!is_array($elements)) {
			print_backtrace('No elements: ' . print_r($elements,true) . DB::lastError());
			//$this->addComponent(new Label(DB::lastError()),'status');
		} else {
		$ks = array_keys($elements);
		foreach ($ks as $k) {
			$element =& $elements[$k];
			if ($this->checkAddingPermissionsFor($element)) {
				$fc =& $this->addLine($elements[$k]);
				$this->objs->addComponent($fc);
			}
		}
		$this->view->redraw();
		}
	}

	function checkAddingPermissionsFor(&$element) {
		return true;
	}

	function getValue(){}

	/* Navigation */
	function filter(){
		$fc =& new CollectionFilterer($this->col);
		$fc->registerCallbacks(array('done'=>callback($this, 'refresh')));
		$this->call($fc);
	}

	function setStartValue($val){
		$last = $this->col->size()-$this->pageSize->getValue()+1;
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
			$this->col->orderBy($fname, "DESC");
			$this->colorder = "";
		} else {
			$this->colorder = $fname;
			$this->col->orderBy($fname, 'ASC');
		}
		$this->refresh();
	}
}
?>