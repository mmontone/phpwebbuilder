<?php

class HTMLHandler extends ViewHandler{}

class ComponentHTMLHandler extends HTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$t =& new HTMLContainer('',array('class'=>'Component'));
		$v->appendChild($t);
		return $v;
	}
}

?>