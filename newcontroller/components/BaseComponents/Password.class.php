<?php
require_once dirname(__FILE__) . '/Input.class.php';

class Password extends Input {
	function Password(&$value_model) {
		parent :: Input($value_model);
	}

	function createNode() {
		parent::createNode();
		$in = & $this->view;
		$in->setTagName('input');
		$in->setAttribute('type', 'password');
	}

/*
	function prepareToRender() {
		parent :: prepareToRender();
		echo "sdaf";
		$this->value_model->setValue($s = '');
	}*/
}
?>