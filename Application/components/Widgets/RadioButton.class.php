<?php

class RadioButton extends Widget {
	function RadioButton(&$radio_group, $value, $callback_actions = array ()){
		$this->rg=&$radio_group;
		$this->value=$value;
		parent::Widget($null,$callback_actions);
	}
	function setValue($value) {
		$this->rg->setValue($this->value);
	}
	function getValue(){
		return $this->rg->getValue()==$this->value;
	}

}

class RadioGroup{
	function RadioGroup(&$value_model, $name=null){
		if ($value_model == null) {
			$this->value_model = & new ValueHolder($null = null);
		}
		else {
			#@typecheck $value_model:ValueModel@#
			$this->value_model = & $value_model;
		}
		$this->name=$name?$name:'radiogroup';
	}
	function setValue($value) {
		if ($value!=$this->getValue())
			$this->value_model->setValue($value);
	}

	function getValue() {
		$v = $this->value_model->getValue();
		return $v;
	}
}

?>