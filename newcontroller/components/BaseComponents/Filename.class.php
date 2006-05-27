<?php
require_once dirname(__FILE__) . '/Input.class.php';

class Filename extends Input {
	function initializeView(&$view) {
		$view->setTagName('input');
		$view->setAttribute('type', 'file');
	}
}
?>