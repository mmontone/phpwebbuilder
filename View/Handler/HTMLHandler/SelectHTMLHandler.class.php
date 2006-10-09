<?php

class SelectHTMLHandler extends WidgetHTMLHandler{
	function prepareToRender(){
		parent::prepareToRender();
		$index =& $this->component->getValueIndex();
		if ($this->component->opts[$index] != null) {
			$this->component->opts[$index]->setAttribute('selected', 'selected');
		}
	}
    function initializeDefaultView(&$view){
		$view->setTagName('select');
		$view->setAttribute('size', (string) $this->component->getSize());
		$view->setAttribute('style', 'overflow:4;');
	}

	function initializeView(&$v){
		$this->component->appendOptions($v);
	}

}
?>