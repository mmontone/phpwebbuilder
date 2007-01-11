<?php

class HTMLHTMLHandler extends WidgetHTMLHandler{
	function & createDefaultView() {
		$t =& new XMLNodeModificationsTracker('span');
		return $t;
	}
	function prepareToRender(){
		$text = $this->component->value_model->getValue();
		$this->view->removeChilds();
		$this->view->appendChild(new PlainTextNode($text));
	}
}
?>