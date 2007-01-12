<?php

class LinkXULHandler extends WidgetXULHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$this->initializeDefaultView($v);
		return $v;
	}
	function prepareToRender(){
		parent::prepareToRender();
		if (!$this->component->targetFrame){
			$this->view->setAttribute('onclick', 'window.location=\''.toAjax($this->component->target).'\';');
		} else {
			$this->view->setAttribute('onclick', 'window.open(\''.toAjax($this->component->target).'\','. toAjax($this->component->targetFrame).');');
		}
		$this->view->addCSSClass('clickable');
	}
	function initializeDefaultView(&$view){
		$view->setTagName('box');
	}
}
?>