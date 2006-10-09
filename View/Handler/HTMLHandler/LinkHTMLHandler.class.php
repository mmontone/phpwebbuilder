<?php

class LinkHTMLHandler extends WidgetHTMLHandler{
    function & createDefaultView() {
		$v = & new XMLNodeModificationsTracker;
		$v->appendChild(new HTMLContainer('',array('id'=>'linkName')));
		$this->component->initializeDefaultView($v);
		return $v;
	}
	function prepareToRender(){
		parent::prepareToRender();
		$this->view->setAttribute('href', toAjax($this->component->target));
		$this->view->addCSSClass('clickable');
	}
}
?>