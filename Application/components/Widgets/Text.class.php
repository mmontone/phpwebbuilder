<?php

class Text extends Widget {
	function valueChanged(& $value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		if ($this->view){
			$text = & $this->value_model->getValue();
			$this->view->removeChilds();
			$this->view->appendChild(new XMLTextNode($text));
			$this->view->redraw();
		}
	}

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