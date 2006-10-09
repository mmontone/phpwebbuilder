<?php

class Link extends Widget
{
	var $target;

	function Link ($target, $text) {
		parent::Widget($vm = null);
		$this->target = $target;
		$this->addComponent(new Label($text), "linkName");
	}
		//TODO Remove view
	function setEvents(& $view) {}
}
?>