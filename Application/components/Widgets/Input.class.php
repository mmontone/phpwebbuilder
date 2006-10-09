<?php

class Input extends Widget {
	function printValue(){
		return toAjax($this->value_model->getValue());
	}
}

?>