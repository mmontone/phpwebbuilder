<?php

class CommandLinkHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$this->initializeDefaultView($v);
		return $v;
	}
	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function initializeView(&$view){}
}
?>