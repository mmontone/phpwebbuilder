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
	function valueChanged(&$value_model, &$params) {
		$text = & $this->component->printValue();
		$this->view->replaceChild(new XMLTextNode($text), $this->view->first_child());
		$this->redraw();
	}

}
?>