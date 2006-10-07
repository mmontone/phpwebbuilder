<?php

class TextAreaComponentHTMLHandler extends HTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$v->setTagName('textarea');
		return $v;
	}
}
?>