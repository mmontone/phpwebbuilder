<?php

class XULHandler extends ViewHandler{}

class WidgetXULHandler extends WidgetHTMLHandler{}

class ComponentXULHandler extends XULHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$t =& new HTMLContainer('',array('class'=>'Component'));
		$v->appendChild($t);
		return $v;
	}
}

class InputXULHandler extends XULHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('textbox');
		$v->setAttribute('size', '10');
		return $v;
	}
}


class TextXULHandler extends TextHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('description');
		$t =& new HTMLContainer;
		$t->setAttribute('class', 'Component');
		$v->appendChild($t);
		return $v;
	}
}

?>