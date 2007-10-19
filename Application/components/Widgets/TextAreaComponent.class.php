<?php

class TextAreaComponent extends Widget{
	function printValue(){
		return $this->value_model->getValue();
	}
	function viewUpdated($params) {
		//&#38;Acirc bug in IE
		if (ord(substr($params,0,1))==194){$params = substr($params,1);}
		parent::viewUpdated($params);
	}
}

?>