<?php

class XULHandler extends ViewHandler{}

class WidgetXULHandler extends WidgetHTMLHandler{
	function updateDisabled(&$vh){
		if ($vh->getValue()) {
			$this->view->setAttribute('disabled','true');
		} else {
			$this->view->removeAttribute('disabled');
		}
	}
}

class ComponentXULHandler extends XULHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$t =& new HTMLContainer('',array('class'=>'Component'));
		$v->appendChild($t);
		return $v;
	}
}

?>