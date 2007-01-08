<?php

class TextAreaComponent extends Widget{
	function printValue(){
		return $this->value_model->getValue();
	}
}

?>