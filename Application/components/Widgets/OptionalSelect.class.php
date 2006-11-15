<?php

class OptionalSelect extends Component {
	var $options;
	var $displayF;
	var $value_model;
	var $selectOption = false;
	var $noopts_msg;

	function OptionalSelect(&$value_model, &$collection, $extra_params=array()) {
		$this->value_model =& $value_model;
		$this->options =& $collection;
		$this->displayF =& $extra_params['displayF'];
		$this->noopts_msg =& $extra_params['noopts_msg'];
		if ($this->noopts_msg == null) {
			$this->noopts_msg = 'No hay objetos creados';
		}
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
			$this->value_model->setValue($n = null);
		}
	}

	function optionsChanged() {
		$this->initialize();
	}

	function noOptionsMessage() {
		return $this->noopts_msg;
	}

	function setNoOptionsMessage($msg) {
		$this->noopts_msg = $msg;
	}

	function selectOptionChanged() {
		$this->select_option->enable($this->selectOption);

		if ($this->selectOption) {
			$this->value_model->setValue($this->options->first());
		}
		else {
			$this->value_model->setValue($n = null);
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

class OptionalComponent extends Component {
	var $comp;
	var $selectOption;

	function OptionalComponent(&$comp) {
		$this->comp =& $comp;
		$this->selectOption = false;

		parent::Component();
	}

	function initialize() {
		$selectOption =& new CheckBox(new AspectAdaptor($this, 'selectOption'));
		$selectOption->onChangeSend('selectOptionChanged', $this);
		$this->addComponent($selectOption, 'selectoption_checkbox');

		$this->addComponent($this->comp, 'opt_comp');
		$this->comp->enable($this->selectOption);
	}

	function selectOptionChanged() {
		$this->opt_comp->enable($this->selectOption);
		$this->callbackWith('select_option_changed', $this->selectOption);
	}

	function setSelectOption(&$value) {
		$this->selectOption =& $value;
	}

	function &getSelectOption() {
		return $this->selectOption;
	}
}

?>