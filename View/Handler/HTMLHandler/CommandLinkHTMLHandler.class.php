<?php

class CommandLinkHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$v->setTagName('a');
		return $v;
	}
	function initializeView(&$view){}
    function printString() {
    	return $this->primPrintString($this->component->printString());
    }
}
?>