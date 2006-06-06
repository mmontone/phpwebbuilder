<?php

require_once dirname(__FILE__) . '/FormComponent.class.php';

class Link extends FormComponent
{
	var $target;

	function Link ($target, $text) {
		parent::FormComponent($vm = null);
		$this->target = $target;
		$this->addComponent(new Label($text), "linkName");
	}

	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function prepareToRender(){
		parent::prepareToRender();
		$this->view->setAttribute('href', toAjax($this->target));
	}
}
?>