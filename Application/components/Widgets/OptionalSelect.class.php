<?php

class OptionalSelect extends Component {
	var $options;
	var $displayF;
	var $value_model;
	var $selectOption;

	function OptionalSelect(&$value_model, &$collection, $displayF=null) {
		$this->value_model =& $value_model;
		$this->options =& $collection;
		$this->displayF =& $displayF;
		$this->selectOption = false;

		parent::Component();
	}

	function initialize() {
		if ($this->options->isEmpty()) {
			$this->addComponent(new Label($this->noOptionsMessage()), 'select_option');
		}
		else {
			$selectOption =& new CheckBox(new AspectAdaptor($this, 'selectOption'));
			$selectOption->onChangeSend('selectOptionChanged', $this);
			$this->addComponent($selectOption, 'selectoption_checkbox');
			$selectopt =& new Select($this->value_model, $this->options, $this->displayF);
			$selectopt->enable($this->selectOption);
			$this->addComponent($selectopt, 'select_option');
			$this->options->addInterestIn('changed', new FunctionObject($this, 'optionsChanged'));
		}
	}

	function optionsChanged() {
		$this->initialize();
	}

	function noOptionsMessage() {
		return 'No hay tipos de documentos creados';
	}

	function selectOptionChanged() {
		$this->select_option->enable($this->selectOption);

		if ($this->selectOption) {
			$this->value_model->setValue($this->options->first());
		}
	}

	function setSelectOption(&$value) {
		$this->selectOption =& $value;
		if (!$value) {
			$this->value_model->setValue($n = null);
		}
	}

	function &getSelectOption() {
		return $this->selectOption;
	}
}

?>