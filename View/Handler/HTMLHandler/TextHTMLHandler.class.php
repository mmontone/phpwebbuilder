<?php

class TextHTMLHandler extends WidgetHTMLHandler{
	function & createDefaultView() {
		$t =& new XMLNodeModificationsTracker('span');
		return $t;
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