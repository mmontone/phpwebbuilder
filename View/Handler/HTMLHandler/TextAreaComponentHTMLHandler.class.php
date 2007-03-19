<?php

class TextAreaComponentHTMLHandler extends WidgetHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$v->setTagName('textarea');
		return $v;
	}
	function getPrintValue(){
		$pv = $this->component->printValue();
		return $pv==''?'&nbsp;':$pv;
	}
	function prepareToRender() {
		$this->view->appendChild(new XMLTextNode($this->getPrintValue()));
	}
	function updateEvent(&$col, &$ev){
		//FIREFOX TextArea Bug
		if ($ev[0]=='onchange'){
			$col->atPut('onblur', $a=array('onblur', $ev[1]));
		}
		parent::updateEvent($col, $ev);

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