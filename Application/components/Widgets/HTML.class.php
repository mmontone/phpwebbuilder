<?php

class HTML extends Widget {
	function HTML($string){
		#@typecheck $string:string#@
		parent::Widget(new ValueHolder($string));
	}
	function setEvents() {}
}
?>