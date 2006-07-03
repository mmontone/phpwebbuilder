<?php

class IndexFieldChooser extends Component {
	var $display;
	var $field;
	var $value;
	var $displayValue;

	function IndexFieldChooser(& $field) {
		$this->field =& $field;
		parent::Component();
	}
	function initialize(){
		$this->display =& new ValueHolder($this->displayValue = 'choose');
		$this->updateDisplay();
	}

	function &getTarget() {
		return $this->field->getTarget();
	}

	function setTarget(&$value) {
		$this->field->setTarget($value);
	}

	function updateDisplay() {
		if ($this->getTarget()) {
			$target =& $this->field->getTarget();
			$this->display->setValue($this->displayValue=$target->indexValues());
		} else {
			$this->display->setValue($this->displayValue = 'choose');
		}
		$this->addComponent(new CommandLink(array('proceedFunction'=>new FunctionObject($this, 'chooseTarget'),'text'=> &$this->displayValue), 'value'), 'setvalue');
	}

	function chooseTarget() {
		$selection = & new CollectionElementChooser($this->field->collection);
		$callback =& new FunctionObject($this, 'targetSelected');
		$selection->registerCallback('selected', $callback);
		$this->call($selection);
	}

	function targetSelected(&$selectedTarget) {
		$this->setTarget($selectedTarget);
		$this->updateDisplay();
	}
}

?>