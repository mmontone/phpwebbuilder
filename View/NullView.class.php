<?php

require_once dirname(__FILE__) . '/XMLNodeModificationsTracker.class.php';

class NullView extends XMLNodeModificationsTracker
{
	function render (){
		return "";
	}
	function getId (){
		return "";
	}
}

?>
