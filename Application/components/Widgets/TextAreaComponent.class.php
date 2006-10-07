<?php

class TextAreaComponent extends Widget{

	function valueChanged(&$value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		$text = & $this->printValue();
		$this->view->replaceChild(new XMLTextNode($text), $this->view->first_child());
		$this->view->redraw();
	}
	function printValue(){
		return toAjax($this->value_model->getValue());
	}
	function prepareToRender() {
		$this->view->appendChild(new XMLTextNode($this->printValue()));
		if ($this->disabled)
			$this->view->setAttribute('readonly','true');
	}
}

?>