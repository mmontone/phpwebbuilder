<?php

class IndexFieldChooser extends Component {
	var $display;
	var $field;
	var $value;

	function IndexFieldChooser(& $field) {
		$this->field =& $field;
		$this->display =& new ValueHolder($v = 'choose');
		$this->updateDisplay();
		$this->addComponent(new CommandLink(array('proceedFunction'=>new FunctionObject($this, 'chooseTarget'),'text'=> &$this->display), 'value'));
	}

	function &getValue() {
		return $this->field->getValue();
	}

	function setValue(&$value) {
		$this->field->setValue($value);
	}

	function updateDisplay() {
		if ($this->getValue() != 0) {
			$target =& $this->field->getTarget();
			$this->display->setValue($target->indexValues());
		}
		else
			$this->display->setValue($s = 'choose');
	}

	function chooseTarget() {
		$selection = & new CollectionElementChooser($this->field->collection);
		$callback =& new FunctionObject($this, 'targetSelected');
		$selection->registerCallback('selected', $callback);
		$this->call($selection);
	}

	function targetSelected(&$selectedTarget) {
		$this->setValue($selectedTarget->getId());
		$this->updateDisplay();
	}
}

?>