<?php

class DateInput extends Input {}

class DateTimeInput extends Widget {
	function initialize(){
		$this->addComponent(new Input, 'date');
		$this->addComponent(new Label(Translator::Translate('choose date')), 'select');
		$this->select->setEvent('onclick', 'displayCalendar(document.getElementById(\'' . $this->getId() . ':date\').firstChild.firstChild,\'yyyy/mm/dd hh:ii\',this,true);');
	}
}

?>