<?php

class DateInput extends Component {
	var $value_model;

	function DateInput(&$value_model) {
		#@typecheck $value_model:ValueModel#@
		$this->value_model =& $value_model;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Input($this->value_model), 'date');
		$this->addComponent(new Label(' '), 'select');
		$this->select->events->atPut('onclick', $a = array('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . CHILD_SEPARATOR.'date\'),\'yyyy-mm-dd\',this,false);'));
	}

	function onEnterClickOn(&$comp) {
		$this->date->onEnterClickOn($comp);
	}
}

class DateTimeInput extends Component {
	var $value_model;

	function DateTimeInput(&$value_model) {
		#@typecheck $value_model:DateTime@#
		$this->value_model =& $value_model;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Input($this->value_model), 'date');
		$this->addComponent(new Label(' '), 'select');
		$this->select->events->atPut('onclick', $a = array('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . CHILD_SEPARATOR.'date\'),\'yyyy-mm-dd hh:ii\',this,true);'));
	}

	function onEnterClickOn(&$comp) {
		$this->date->onEnterClickOn($comp);
	}
}

?>