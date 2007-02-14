<?php

class TextAreaComponentHTMLHandler extends WidgetHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$v->setTagName('textarea');
		return $v;
	}
	function getPrintValue(){
		$pv = $this->component->printValue();
		return $pv==''?'&amp;nbsp;':$pv;
	}
	function prepareToRender() {
		$this->view->appendChild(new XMLTextNode($this->getPrintValue()));
	}
	function updateDisabled(&$vh){
		if ($vh->getValue()) {
			$this->view->setAttribute('readonly','true');
		} else {
			$this->view->removeAttribute('readonly');
		}
	}
	function valueChanged(&$value_model, &$params) {
		$text = $this->getPrintValue();
		$this->view->replaceChild(new XMLTextNode($text), $this->view->first_child());
		$this->redraw();
	}

}
?>