<?php

class HTML extends Widget {
	function HTML(&$string){

		if (is_object($string)) {
			#@typecheck $string:ValueModel#@
			parent::Widget($string);
		} else {
			#@typecheck $string:string#@
			parent::Widget(new ValueHolder($string));
		}
	}
	function setEvents() {}
}
?>