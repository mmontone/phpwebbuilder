<?php

class HTMLHTMLHandler extends HTMLHandler{
	function & createDefaultView() {
		$t =& new XMLNodeModificationsTracker('span');
		return $t;
	}
}
?>