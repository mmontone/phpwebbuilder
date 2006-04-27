<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Input extends FormComponent{
	function Input ($val=''){
		parent::FormComponent();
		$this->value=$val;
	}
	function createNode(){
		$in =& $this->view;
		$in->setTagName('input');
		$in->setAttribute('value', $this->value);
	}
	function setValue($val){
		$this->value=$val;
		$in =& $this->view;
		$in->setAttribute('value', $val);
	}
}

?>