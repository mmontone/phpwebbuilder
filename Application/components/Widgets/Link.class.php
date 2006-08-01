<?php

require_once dirname(__FILE__) . '/Widget.class.php';

class Link extends Widget
{
	var $target;

	function Link ($target, $text) {
		parent::Widget($vm = null);
		$this->target = $target;
		$this->addComponent(new Label($text), "linkName");
	}

	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function prepareToRender(){
		parent::prepareToRender();
		$this->view->setAttribute('href', toAjax($this->target));
		$this->view->addCSSClass('clickable');
	}
}
?>