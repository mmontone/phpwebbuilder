<?php

class JSCommandLinkHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$this->component->initializeDefaultView($v);
		return $v;
	}
	function initializeDefaultView(&$view){
		$view->setTagName('a');
		$fun =& $this->target->getMainFunction();
		$view->setAttribute('onClick', "javascript:$fun();");
	}
	function initializeView(&$view){}
}
?>