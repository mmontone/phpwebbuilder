<?php

class Text extends Widget {
	function valueChanged(& $value_model, &$params) {
		if ($this->view){
			$this->prepareToRender();
			$this->view->redraw();
		}
	}
	function setEvents(& $view) {}

	function & createDefaultView() {
		$t =& new XMLNodeModificationsTracker('span');
		return $t;
	}
	function prepareToRender(){
		$text =& $this->value_model->getValue();
		$this->view->removeChilds();
		$this->view->appendChild(new XMLTextNode($text));
	}
	function printTree() {
		return $this->value_model->getValue();
	}
}
?>