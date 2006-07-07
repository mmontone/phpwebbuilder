<?php

class Password extends Input {

	function initializeDefaultView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'password');
	}
}
?>