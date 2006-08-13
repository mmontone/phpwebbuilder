<?php

class NullView extends XMLNodeModificationsTracker
{
	function redraw(){}
	function render (){
		return "";
	}
	function getId (){
		return "";
	}
}

?>
