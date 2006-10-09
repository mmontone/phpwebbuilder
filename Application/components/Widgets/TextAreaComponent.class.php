<?php

class TextAreaComponent extends Widget{
	//TODO Remove view
	function valueChanged(&$value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		$text = & $this->printValue();
		$this->view->replaceChild(new XMLTextNode($text), $this->view->first_child());
		$this->redraw();
	}
	function printValue(){
		return toAjax($this->value_model->getValue());
	}
}

?>