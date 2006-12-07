<?php

class TextHTMLHandler extends WidgetHTMLHandler{
	function & createDefaultView() {
		$t =& new XMLNodeModificationsTracker('span');
		return $t;
	}
	function prepareToRender(){
		$text =& $this->component->value_model->getValue();

		if (is_object($text)) {
			$t =& $text->printString();
		} else {
			$t =& $text;
		}

		$this->view->removeChilds();
		$this->view->appendChild(new XMLTextNode($t));
	}
	function valueChanged(& $value_model, &$params) {
		if ($this->view){
			$this->prepareToRender();
			$this->redraw();
		}
	}
}
?>