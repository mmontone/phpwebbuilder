<?php
class CollectionNavigator extends Component {
	var $col;
	var $fields;
	var $classN;

	function CollectionNavigator(&$col, $fields=null, $callbacks=array()) {
		parent::Component();

		if ($col == null) print_backtrace('No collection');
		$this->col = & $col;
		$this->classN = $col->getDataType();

		if ($fields==null){
			$this->fields = $col->allFields();
		} else {
			$this->fields =& $fields;
		}

		$this->registerCallbacks($callbacks);
	}

	function initialize() {
		$this->col->addInterestIn('changed', new FunctionObject($this, 'refresh'), array('execute once' => true));
        $this->addNavigationButtons();
		$this->addComponent(new ActionLink($this, 'filter', 'filter', $n = null), 'filter');
		$this->addComponent(new ActionLink($this, 'refresh', 'refresh', $n = null), 'refresh');
		$this->firstElement =& new ValueHolder($fp = 1);
        $this->firstElement->addInterestIn('changed', new FunctionObject($this, 'refresh'), array('execute once' =>true));
		$this->addComponent(new Input($this->firstElement), 'firstElem');
		$this->addComponent(new CompositeWidget, 'objs');
		/* Size */
		$this->size =& new ValueHolder($s = 0);
		$this->addComponent(new Text($this->size), 'realSize');
		$this->pageSize =& new ValueHolder($pz = 0);
        $this->pageSize->addInterestIn('changed', new FunctionObject($this, 'refresh'), array('execute once' => true));
        $this->pageSize->setValue(5);
		$this->addComponent(new Input($this->pageSize), 'pSize');
		foreach ($this->fields as $n=>$f) {
			$fc = & new CompositeWidget;
			if (is_string($f)) {
				$this->addComponent($fc, $f);
				$fc->addComponent(new ActionLink($this, 'sort', $f, $n));
			} else {
				$this->addComponent($fc, $f->varName);
				$fc->addComponent(new ActionLink($this, 'sort', $f->displayString, $f->varName));
			}
		}
	}

	function addNavigationButtons() {
		$this->addComponent(new ActionLink($this, 'nextPage', 'next', $n = null), 'next');
		$this->addComponent(new ActionLink($this, 'prevPage', 'prev', $n = null), 'prev');
		$this->addComponent(new ActionLink($this, 'firstPage', 'first', $n = null), 'first');
		$this->addComponent(new ActionLink($this, 'lastPage', 'last', $n = null), 'last');
	}

	function removeNavigationButtons() {
		$this->deleteComponentAt('next');
		$this->deleteComponentAt('prev');
		$this->deleteComponentAt('first');
		$this->deleteComponentAt('last');
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

        $this->showElements();
	}

    function showElements() {
    	$elements =& $this->getElements();

        if (is_array($elements)) {
            $ks = array_keys($elements);
            foreach ($ks as $k) {
                $fc =& $this->addLine($elements[$k]);
                $this->objs->addComponent($fc);
            }
            $this->redraw();
        }
    }

    #@php4
    function &getElements() {
        $elems =& $this->col->elements();
        if (!is_array($elems)) {
            $dialog =& ErrorDialog::Create(DBSession::lastError());
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
        }
        return $elems;
    }//@#

    #@php5
    function &getElements() {
        try {
        	return $this->col->elements();
        } catch (DBError $e) {
        	$dialog =& ErrorDialog::Create($e->getMessage());
            $dialog->onAccept(new FunctionObject($this, 'doNothing'));
            $this->call($dialog);
        }
    }//@#

    function doNothing() {

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