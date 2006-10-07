<?php

class WidgetHTMLHandler extends HTMLHandler{
	function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$this->component->initializeDefaultView($v);
		return $v;
	}
}
?>