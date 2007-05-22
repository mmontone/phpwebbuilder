<?php
class CollectionNavigator extends Component {
	var $col;
	var $fields;
	var $classN;

	function CollectionNavigator(& $col, $fields = null, $callbacks = array ()) {
		parent :: Component();

		if ($col == null)
			print_backtrace('No collection');
		$this->col = & $col;
		$this->classN = $col->getDataType();

		if ($fields == null) {
			$this->fields = $col->allFields();
		} else {
			$this->fields = & $fields;
		}

		$this->registerCallbacks($callbacks);
	}

	function initialize() {
		$this->col->addInterestIn('changed', new FunctionObject($this, 'refresh'), array (
			'execute once' => true
		));
		$this->addNavigationButtons();
		$this->addComponent(new ActionLink($this, 'filter', 'filter', $n = null), 'filter');
		$this->addComponent(new ActionLink($this, 'refresh', 'refresh', $n = null), 'refresh');
		$this->firstElement = & new ValueHolder($fp = 1);
		$this->firstElement->addInterestIn('changed', new FunctionObject($this, 'refresh'), array (
			'execute once' => true
		));
		$this->addComponent(new Input($this->firstElement), 'firstElem');
		$this->addComponent(new CompositeWidget, 'objs');
		/* Size */
		$this->size = & new ValueHolder($s = 0);
		$this->addComponent(new Text($this->size), 'realSize');
		$this->pageSize = & new ValueHolder($pz = 0);
		$this->pageSize->addInterestIn('changed', new FunctionObject($this, 'refresh'), array (
			'execute once' => true
		));
		$this->pageSize->setValue(5);
		$this->addComponent(new Input($this->pageSize), 'pSize');

		$this->addOrderBar();
	}

	function addOrderBar() {
		if (is_array($this->fields) and !empty ($this->fields)) {
			$bar = & new OrderBar;
			foreach ($this->fields as $n => $f) {
				if (is_string($f)) {
					$exp = & new AttrPathExpression('', $f);
					$slot = $f;
				} else {
					if (is_a($f, 'DataField')) {
						$exp = & new AttrPathExpression('', $f->colName);
						$slot = $f->colName;
					} else {
						#@typecheck $exp : PathExpression@#
						$exp = & $f;
						$slot = & $f->attr;
					}
				}

				$bar->addSortLink(new SortLink(array (
					'text' => Translator :: Translate(ucfirst($n
				)), 'exp' => & $exp, 'collection' => & $this->col)), 'orderby_' . $slot);
			}
			$this->addComponent($bar, 'order_bar');
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

	function refresh() {
		$col = & $this->col;
		$col->limit = $this->pageSize->getValue();
		$col->offset = $this->firstElement->getValue() - 1;
		$this->size->setValue($col->size());
		$this->objs->deleteChildren();
		// The next line is only necessary if elements are not added or removed
		// through the collection interface (the in-memory collection doesn't get notified)
		$col->refresh();

		$this->showElements();
	}

	function showElements() {
		$elements = & $this->getElements();

		if (is_array($elements)) {
			$ks = array_keys($elements);
			foreach ($ks as $k) {
				$fc = & $this->addLine($elements[$k]);
				$this->objs->addComponent($fc);
			}
			$this->redraw();
		}
	}

	#@php4
	function & getElements() {
		$elems = & $this->col->elements();
		if (!is_array($elems)) {
			$dialog = & ErrorDialog :: Create(DBSession :: lastError());
			$dialog->onAccept(new FunctionObject($this, 'doNothing'));
			$this->call($dialog);
		}
		return $elems;
	} //@#

	#@php5
	function & getElements() {
		try {
			return $this->col->elements();
		} catch (DBError $e) {
			$dialog = & ErrorDialog :: Create($e->getMessage());
			$dialog->onAccept(new FunctionObject($this, 'doNothing'));
			$this->call($dialog);
		}
	} //@#

	function doNothing() {

	}

	function getValue() {
	}

	/* Navigation */
	function filter() {
		$fc = & new CollectionFilterer($this->col);
		$fc->registerCallbacks(array (
			'done' => callback($this,
			'refresh'
		)));
		$this->call($fc);
	}

	function setStartValue($val) {
		$last = $this->col->size() - $this->pageSize->getValue() + 1;
		$this->firstElement->setValue($r = max(min($val, $last), 1));
	}

	function prevPage() {
		$this->setStartValue($this->firstElement->getValue() - $this->pageSize->getValue());
	}

	function nextPage() {
		$this->setStartValue($this->firstElement->getValue() + $this->pageSize->getValue());
	}

	function firstPage() {
		$this->setStartValue($r = 1);
	}

	function lastPage() {
		$this->setStartValue($this->col->size());
	}
}

class SortLink extends Widget {
	var $state = null;
	var $collection;
	var $exp;
	var $text;
	var $unorder = true;

	function SortLink($params) {
		parent :: Widget($v = null);
		#@typecheck $params['collection'] : Collection@#
		#@typecheck $params['exp'] : PathExpression@#
		#@typecheck $params['text'] : string@#
		$this->collection = & $params['collection'];
		$this->exp = & $params['exp'];
		$this->text = $params['text'];
		if (isset ($params['unorder'])) {
			$this->unorder = $params['unorder'];
		}
    }

    function setEvents() {
	}

	function initialize() {
		$this->addComponent(new Label($this->text), 'linkName');
		$this->onClickSend('execute', $this);
		$this->addStateComponent();
	}

	function addStateComponent() {
		switch ($this->state) {
			case 'ASC' :
				$this->addComponent(new Label(' (Asc)'), 'mark');
				$this->onClickSend('execute', $this);
				$this->setComponentState('unordered', false);
				$this->setComponentState('ordered', true);
				break;
			case 'DESC' :
				$this->addComponent(new Label(' (Desc)'), 'mark');
				$this->onClickSend('execute', $this);
				$this->setComponentState('unordered', false);
				$this->setComponentState('ordered', true);
				break;
			default : /* null */
				$this->addComponent(new Label(''), 'mark');
				$this->onClickSend('execute', $this);
				$this->setComponentState('unordered',true);
				$this->setComponentState('ordered', false);
		}
	}

	function setState($state) {
		$this->state = $state;
	}

	function getState() {
		return $this->state;
	}

	function nextState() {
		if ($this->state == 'ASC') {
			$this->state = 'DESC';
		} else {
			$this->state = 'ASC';
		}

		return $this->state;
	}

	function execute() {
		if ($this->unorder) {
			$this->collection->unordered();
		}
		$this->collection->orderByPath($this->exp, $this->nextState());
		$this->addStateComponent();
		$this->triggerEvent('executed', $this);
	}

    function printString() {
    	return $this->primPrintString(' state: ' . $this->state . ' exp: ' . $this->exp->path . '.' . $this->exp->attr);
    }
}

class OrderBar extends Component {
    var $links = array();

    function addSortLink(&$sortlink, $slot) {
		$this->addComponent($sortlink, $slot);
        $this->links[] =& $sortlink;
        $sortlink->addInterestIn('executed', new FunctionObject($this, 'sortLinkExecuted'));
	}

    function sortLinkExecuted(&$sort_link) {
        foreach (array_keys($this->links) as $l) {
        	$link =& $this->links[$l];
            if (!$link->is($sort_link)) {
                $link =& $this->links[$l];
                $link->state = null;
                $link->addStateComponent();
            }
        }
    }
}

?>