<?php

class HTML extends Widget {
	function HTML($string){
		parent::Widget(new ValueHolder($string));
	}
	function setEvents() {}
}
?>