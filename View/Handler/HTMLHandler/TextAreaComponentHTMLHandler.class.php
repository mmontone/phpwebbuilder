<?php

class TextAreaComponentHTMLHandler extends WidgetHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$v->setTagName('textarea');
		return $v;
	}
	function prepareToRender() {
		$this->view->appendChild(new XMLTextNode($this->component->printValue()));
		if ($this->component->disabled)
			$this->view->setAttribute('readonly','true');
	}
}
?>