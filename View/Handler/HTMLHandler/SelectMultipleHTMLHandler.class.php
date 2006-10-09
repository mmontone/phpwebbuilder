<?php

class SelectMultipleHTMLHandler extends WidgetHTMLHandler{
    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('multiple', 'multiple');
		//$view->setAttribute('size', (string) $this->getSize());
	}

	function initializeView(&$v){
		$this->appendOptions($v);
	}
}
?>