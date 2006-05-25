<?php

class IndexFieldChooser extends FormComponent {
	var $display;
	var $field;

	function IndexFieldChooser(& $field) {
		parent::FormComponent(new ValueHolder($field->getValue()));
		$this->field = $field;
		$this->display =& new ValueHolder($v = 'choose');
		$this->updateDisplay();
		$this->addComponent(new ActionLink2(array('action'=>new FunctionObject($this, 'chooseTarget'),'text'=> &$this->display), 'value'));
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
		$selection = & new SelectCollectionComponent($this->field->collection);
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