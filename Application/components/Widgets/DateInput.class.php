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
		$this->addComponent(new Label('&nbsp;'), 'select');
		$this->select->events->atPut('onclick', $a = array('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . CHILD_SEPARATOR.'date\'),\''.$this->getDateFormatString().'\',this,false);'));
	}

	function onEnterClickOn(&$comp) {
		$this->date->onEnterClickOn($comp);
	}
	function getDateFormatString(){
		return 'yyyy-mm-dd';
	}
}

class DateTimeInput extends DateInput {
	function initialize() {
		parent::initialize();
		$this->select->events->atPut('onclick', $a = array('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . CHILD_SEPARATOR.'date\'),\''.$this->getDateFormatString().'\',this,true);'));
	}
	function getDateFormatString(){
		return 'yyyy-mm-dd hh:mm';
	}
}

?>