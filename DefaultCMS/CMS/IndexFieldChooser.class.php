<?php

class IndexFieldChooser extends Component {
	var $display;
	var $field;
	var $value;

	function IndexFieldChooser(& $field) {
		$this->field = $field;
		$var = $this->field->getValue();
		$this->value =& new ValueHolder($var);
		$this->display =& new ValueHolder($v = 'choose');
		$this->updateDisplay();
		$this->addComponent(new ActionLink2(array('action'=>new FunctionObject($this, 'chooseTarget'),'text'=> &$this->display), 'value'));
	}

	function &getValue() {
		return $this->value->getValue();
	}

	function setValue(&$value) {
		$this->value->setValue($value);
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
		$this->field->setValue($selectedTarget->getId());
		$this->setValue($this->field->getValue());
		$this->updateDisplay();
	}
}

?>