<?php

class PasswordHTMLHandler extends InputHTMLHandler{
	function initializeDefaultView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'password');
	}
}
?>