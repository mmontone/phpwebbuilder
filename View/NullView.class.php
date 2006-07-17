<?php

require_once 'XML/XMLNodeModificationsTracker.class.php';

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
