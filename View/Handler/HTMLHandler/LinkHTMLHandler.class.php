<?php

class LinkHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$this->initializeDefaultView($v);
		return $v;
	}
	function prepareToRender(){
		parent::prepareToRender();
		$this->view->setAttribute('href', toAjax($this->component->target));
		if ($this->component->targetFrame){
			$this->view->setAttribute('target', toAjax($this->component->targetFrame));
		}
		$this->view->addCSSClass('clickable');
	}
	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function setEvents(&$comp){}
}

class ImageLinkHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$this->initializeDefaultView($v);
		return $v;
	}
	function prepareToRender(){
		parent::prepareToRender();
		$this->view->setAttribute('src', toAjax($this->component->target));
	}
	function initializeDefaultView(&$view){
		$view->setTagName('img');
	}
	function setEvents(&$comp){}
}

?>