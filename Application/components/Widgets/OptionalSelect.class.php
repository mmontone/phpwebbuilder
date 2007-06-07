<?php

class OptionalSelect extends Component {
	var $options;
	var $displayF;
	var $value_model;
	var $selectOption;
	var $noopts_msg;

	function OptionalSelect(&$value_model, &$collection, $extra_params=array()) {
		#@typecheck $value_model:ValueModel,$collection:Collection@#
		$this->value_model =& $value_model;
		$this->options =& $collection;
		$this->displayF =& $extra_params['displayF'];
		$this->noopts_msg =& $extra_params['noopts_msg'];
		if ($this->noopts_msg == null) {
			$this->noopts_msg = Translator::translate('No hay objetos creados');
		}
		$this->selectOption =& new ValueHolder(false);

		parent::Component();
	}

	function initialize() {
		if ($this->options->isEmpty()) {
			$this->addComponent(new Label($this->noOptionsMessage()), 'select_option');
		}
		else {
			$selectOption =& new CheckBox($this->selectOption);
			$selectOption->onChangeSend('selectOptionChanged', $this);
			$this->addComponent($selectOption, 'selectoption_checkbox');
			$selectopt =& new Select($this->value_model, $this->options, $this->displayF);
			$selectopt->enable($this->selectOption->getValue());
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
		$this->select_option->enable($this->selectOption->getValue());

		if ($this->selectOption->getValue()) {
			$this->value_model->setValue($this->options->first());
		}
		else {
			$this->value_model->setValue($n = null);
		}
	}
}

class OptionalComponent extends Component {
	var $comp;
	var $selectOption;

	function OptionalComponent(&$comp) {
		#@typecheck $comp:Component@#
		$this->comp =& $comp;
		$this->selectOption = false;

		parent::Component();
	}
	function onEnterClickOn(){}
	function initialize() {
		$selectOption =& new CheckBox(new AspectAdaptor($this, 'selectOption'));
		$selectOption->onChangeSend('selectOptionChanged', $this);
		$this->addComponent($selectOption, 'selectoption_checkbox');

		$this->addComponent($this->comp, 'opt_comp');
		$this->comp->onChangeSend('changed', $this);
		$this->comp->enable($this->selectOption);
	}

	function selectOptionChanged() {
		$this->opt_comp->enable($this->selectOption);
		$this->callbackWith('select_option_changed', $this->selectOption);
		$this->changed();
	}

	function setSelectOption(&$value) {
		$this->selectOption =& $value;
	}

	function &getSelectOption() {
		return $this->selectOption;
	}
}

?>