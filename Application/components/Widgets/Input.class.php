<?php

class Input extends Widget {
	function printValue(){
		return $this->value_model->getValue();
	}
}

?>