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
			$target =& $this->getTarget();
			$this->display->setValue($this->displayValue=$target->printString());
            $this->addComponent(new CommandLink(array('text' => 'edit', 'proceedFunction' => new FunctionObject($this, 'editTarget'))), 'edit_target');
		} else {
			$this->display->setValue($this->displayValue = 'choose');
            $this->deleteComponentAt('edit_target');
		}
		$this->addComponent(new CommandLink(array('proceedFunction'=>new FunctionObject($this, 'chooseTarget'),'text'=> &$this->displayValue), 'value'), 'setvalue');
	}

    function editTarget() {
        $this->call(new PersistentObjectEditor($this->getTarget()));
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