<?php

class CommandLink extends Widget{
	var $proceed;
	var $revert;
    function CommandLink($params) {
		parent::Widget($vm = null);
		$this->proceed =& $params['proceedFunction'];
		$this->revert =& $params['revertFunction'];
		$this->addComponent(new Label($params['text']), "linkName");
		$this->onClickSend('execute', $this);
    }
	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function initializeView(&$view){}
	function execute(){
		this->proceed->call();
	}
	function setOnClickEvent(&$view){
		parent::setOnClickEvent($view);
		$view->setAttribute('onclick', $view->getAttribute('onclick').'var ev = getEvent(event); ev.returnValue=false;return false;');
	}

}
?>