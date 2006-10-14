<?php

class InputXULHandler extends WidgetXULHandler{
	function prepareToRender(){
		$this->valueChanged($this->component->value_model, $n=null);
	}
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('textbox');
	//	$v->setAttribute('size', '10');
		return $v;
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->view){
			$this->view->setAttribute('value', $this->component->printValue());
		}
	}
}

class CommandLinkXULHandler extends WidgetXULHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->setAttribute('label', $this->component->textv);
		$v->setTagName('button');
		return $v;
	}
}


class TextXULHandler extends TextHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('description');
		return $v;
	}
}

class CheckBoxXULHandler extends WidgetXULHandler{
/* <checkbox id="case-sensitive" checked="true" label=""/> */
	function prepareToRender() {
		parent::prepareToRender();
		$this->valueChanged($this->component->value_model, $n=null);
	}
	function initializeDefaultView(&$view) {
		$view->setTagName('checkbox');
	}
	function valueChanged(& $value_model, & $params) {
			if ($this->component->getValue()) {
				$this->view->setAttribute('checked', 'true');
			} else {
				$this->view->removeAttribute('checked');
			}
	}
}

class LinkXULHandler extends WidgetXULHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker('button');
		$v->setAttribute('label', $this->component->target);
		return $v;
	}
}
class PasswordXULHandler extends InputXULHandler{
	function &createDefaultView(){
		$v =& parent::createDefaultView();
		$v->setAttribute('type', 'password');
		return $v;
	}
}
class RadioButtonXULHandler extends WidgetXULHandler{
/*<radio id="orange" label="Orange"/>*/
    function initializeView(&$view){
		$view->setTagName('radio');
	}
	function valueChanged(&$value_model, &$params) {
		if ($this->component->value_model->getValue()) {
			$this->view->setAttribute('selected','true');
		} else{
			$this->view->removeAttribute('selected');
		}
	}
}
class TextAreaComponentXULHandler extends WidgetXULHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker('textbox');
	//	$v->setAttribute('size', '10');
		$v->setAttribute('multiline', 'true');
		return $v;
	}
}


class FilenameXULHandler extends WidgetXULHandler{}
class HTMLXULHandler extends WidgetXULHandler{}
?>