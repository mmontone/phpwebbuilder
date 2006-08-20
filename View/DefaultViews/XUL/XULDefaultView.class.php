<?php

class XULDefaultView extends PWBFactory{
}

class ComponentXULDefaultView extends XULDefaultView{
	function &createInstanceFor(&$component){
		$v =& new XMLNodeModificationsTracker('box');
		$t =& new HTMLContainer;
		$t->setAttribute('class', 'Component');
		$v->appendChild($t);
		return $v;
	}
}


class InputXULDefaultView extends XULDefaultView{
	function &createInstanceFor(&$component){
		$v =& new XMLNodeModificationsTracker('textbox');
		$v->setAttribute('size', '10');
		return $v;
	}
}

class TextXULDefaultView extends XULDefaultView{
	function &createInstanceFor(&$component){
		$v =& new XMLNodeModificationsTracker('description');
		$t =& new HTMLContainer;
		$t->setAttribute('class', 'Component');
		$v->appendChild($t);

		return $v;
	}
}
class CommandLinkXULDefaultView extends XULDefaultView{
	function &createInstanceFor(&$component){
		$v =& new XMLNodeModificationsTracker('button');
		$t =& new HTMLContainer;
		$t->setAttribute('class', 'Component');
		$v->appendChild($t);
		return$v;
	}
}

?>