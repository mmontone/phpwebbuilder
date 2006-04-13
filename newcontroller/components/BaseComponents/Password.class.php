<?php

require_once dirname(__FILE__) . '/../../Component.class.php';

class Password extends FormComponent{
	function Password ($name){
		parent::FormComponent();
		$this->id = $name;
		$this->value=''; 
	}
	function createNode(){
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('type', 'password');
	}
	function setValue($val){
		$this->value=$val;
		$in =& $this->view;
		$in->setAttribute('value', $val);
	}
}

?>