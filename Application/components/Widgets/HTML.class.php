<?php

class HTML extends Widget {
	function HTML($string){
		parent::Widget(new ValueHolder($string));
	}
	function valueChanged(& $value_model, &$params) {
		if ($this->view){
			$this->prepareToRender();
			$this->view->redraw();
		}
	}
	function setEvents(& $view) {}

	function prepareToRender(){
		$text =& $this->value_model->getValue();
		$this->view->removeChilds();
		$this->view->appendChild(new PlainTextNode($text));
	}
	function printTree() {
		return $this->value_model->getValue();
	}
}
?>