<?php

class TextXULHandler extends TextHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('description');
		return $v;
	}
	function prepareToRender(){
		$text =& $this->component->value_model->getValue();
		$this->view->removeChilds();
		$this->view->appendChild(new XMLTextNode($text));
	}
	function valueChanged(& $value_model, &$params) {
		if ($this->view){
			$this->prepareToRender();
			$this->redraw();
		}
	}
}
?>