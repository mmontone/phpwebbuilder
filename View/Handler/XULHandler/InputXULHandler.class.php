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

class PasswordXULHandler extends InputXULHandler{
	function &createDefaultView(){
		$v =& parent::createDefaultView();
		$v->setAttribute('type', 'password');
		return $v;
	}
}

class FilenameXULHandler extends WidgetXULHandler{}
class HTMLXULHandler extends WidgetXULHandler{}
?>