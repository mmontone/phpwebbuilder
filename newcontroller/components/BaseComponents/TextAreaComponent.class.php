<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class TextAreaComponent extends FormComponent{
	function initializeView(&$view){
		$view->setTagName('textarea');
	}

	function prepareToRender(){
		$this->view->append_child(new XMLTextNode($this->printValue()));
	}
	function valueChanged(&$value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		$text = & $this->printValue();
		$this->view->replace_child(new XMLTextNode($text), $this->view->first_child());
		$this->view->redraw();
	}
	function printValue(){
		return toAjax($this->value_model->getValue());
	}
}

?>