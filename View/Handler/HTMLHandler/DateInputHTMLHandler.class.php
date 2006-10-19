<?php

//	<tr><td>Date input 3(YYYY/MM/DD hh:ii): </td><td><td><input type="text" value="2006/06/02 12:55" readonly name="theDate3">
// <input type="button" value="Cal" onclick="displayCalendar(document.forms[0].theDate3,'yyyy/mm/dd hh:ii',this,true)"></td></tr>

class DateTimeInputHTMLHandler extends InputHTMLHandler {
	function initializeDefaultView(&$view){
		$date_input =& $this->getComponent();
		$date_field =& $date_input->value_model;

		$date_node =& new XMLNodeModificationsTracker('input');
		//$date_node->setTagName('input');
		$date_node->setAttribute('type', 'text');
		$date_node->setAttribute('value', $date_field->getValue());
		//$date_node->setAttribute('readonly', 'readonly');
		//$date_node->setAttribute('name', $date_input->getId());

		$cal =& new XMLNodeModificationsTracker('input');
		//$date_node->setTagName('input');
		$cal->setAttribute('type', 'button');
		$cal->setAttribute('value', '...');
		$cal->setAttribute('onclick', 'displayCalendar(document.getElementById(\'' . $date_input->getId() . '\').firstChild.firstChild,\'yyyy/mm/dd hh:ii\',this,true);');

		$view->setTagName('div');
		$date_container =& new XMLNodeModificationsTracker('div');
		$date_container->appendChild($date_node);

		$cal_container =& new XMLNodeModificationsTracker('div');
		$cal_container->appendChild($cal);

		$view->appendChild($date_container);
		$view->appendChild($cal_container);
	}
}

?>