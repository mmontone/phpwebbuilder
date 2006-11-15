<?php

class DateInput extends Input {
	function DateInput(&$value_model) {
		$this->value_model =& $value_model;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Input($this->value_model), 'date');
		$this->addComponent(new Label(''), 'select');
		$this->select->events->atPut('onclick', $a = array('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . CHILD_SEPARATOR.'date\'),\'yyyy-mm-dd\',this,true);'));
	}

}

class DateTimeInput extends Component {
	function DateTimeInput(&$value_model) {
		$this->value_model =& $value_model;
		parent::Component();
	}

	function initialize() {
		$this->addComponent(new Input($this->value_model), 'date');
		$this->addComponent(new Label(''), 'select');
		$this->select->events->atPut('onclick', $a = array('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . CHILD_SEPARATOR.'date\'),\'yyyy-mm-dd hh:ii\',this,true);'));
	}
}

?>