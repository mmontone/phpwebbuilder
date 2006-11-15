<?php

/*
class DateTimeInputHTMLHandler extends InputHTMLHandler {
	function initializeDefaultView(&$view){
		$date_input =& $this->getComponent();
		$date_field =& $date_input->value_model;

		$date_node =& new XMLNodeModificationsTracker('input');
		$date_node->setAttribute('type', 'text');
		$date_node->setAttribute('value', $date_field->getValue());

		$cal =& new XMLNodeModificationsTracker('input');
		$cal->setAttribute('type', 'button');
		$cal->setAttribute('value', '...');
		$cal->setAttribute('onclick', 'displayCalendar(document.getElementById(\'' . $date_input->getId() . '\'),\'yyyy/mm/dd hh:ii\',this,true);');

		$view->setTagName('div');
		$view->appendChild($date_node);
		$view->appendChild($cal);

	}

	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$date_input =& $this->getDateInputNode();
			$this->view->setAttribute('value', $this->component->printValue());
		}
	}

	function &getDateInputNode() {
		$div =& $this->view->first_child();
		return $div->first_child();
	}

	function updateEvent(&$col, &$ev){
		print_backtrace();
		$date_node =& $this->getDateInputNode();
		$date_node->setAttribute($ev[0], $ev[1]);
	}
}

*/



?>