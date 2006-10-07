<?php

class HTMLHandler extends ViewHandler{
	function &createDefaultView() {
		return $this->component->createDefaultView();
	}
}

class ComponentHTMLHandler extends HTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$t =& new HTMLContainer('',array('class'=>'Component'));
		$v->appendChild($t);
		return $v;
	}
}

?>