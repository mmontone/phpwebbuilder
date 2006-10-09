<?php

class Link extends Widget
{
	var $target;
	var $targetFrame = null;
	function Link ($target, $text, $targetFrame=null) {
		parent::Widget($vm = null);
		$this->target = $target;
		$this->addComponent(new Label($text), "linkName");
		$this->targetFrame = $targetFrame;
	}
	function setEvents() {}
}
?>