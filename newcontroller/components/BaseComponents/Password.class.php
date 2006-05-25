<?php
require_once dirname(__FILE__) . '/Input.class.php';

class Password extends Input {
	function Password(&$value_model) {
		parent :: Input($value_model);
	}

	function initializeView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'password');
	}
}
?>