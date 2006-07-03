<?php

require_once dirname(__FILE__) . '/Widget.class.php';

class TextAreaComponent extends Widget{

	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$v->setTagName('textarea');
		return $v;
	}
	function prepareToRender(){
		$this->view->appendChild(new XMLTextNode($this->printValue()));
	}
	function valueChanged(&$value_model, &$params) {
		/*WARNING!!! If there's an error, look here first ;) */
		$text = & $this->printValue();
		$this->view->replaceChild(new XMLTextNode($text), $this->view->first_child());
		$this->view->redraw();
	}
	function printValue(){
		return toAjax($this->value_model->getValue());
	}
}

?>