<?php

class DateTimeInputHTMLHandler extends InputHTMLHandler {
	function initializeDefaultView(&$view){
		$date_input =& $this->getComponent();
		$date_field =& $date_input->value_model;

		$date_node =& $view;
		$date_node->setTagName('input');
		$date_node->setAttribute('type', 'text');
		$date_node->setAttribute('value', $date_field->getValue());

		$cal =& new XMLNodeModificationsTracker('input');
		$cal->setAttribute('type', 'button');
		$cal->setAttribute('value', '...');
		$cal->setAttribute('onclick', 'displayCalendar(document.getElementById(\'' . $date_input->getId() . '\'),\'yyyy/mm/dd hh:ii\',this,true);');

		$view->appendChild($cal);
	}
}

?>