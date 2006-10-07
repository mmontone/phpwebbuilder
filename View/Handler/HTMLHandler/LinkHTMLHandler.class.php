<?php

class LinkHTMLHandler extends HTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$this->component->initializeDefaultView($v);
		return $v;
	}
}
?>