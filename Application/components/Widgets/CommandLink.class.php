<?php

class CommandLink extends Widget{
	var $proceed;
	var $revert;
	var $textv;
    function CommandLink($params) {
		$this->proceed =& $params['proceedFunction'];
		$this->revert =& $params['revertFunction'];
		$this->textv =& $params['text'];
		parent::Widget($vm = null);
    }
    function checkAddingPermissions(){
		if (!$this->proceed) print_backtrace();
		return $this->proceed->hasPermissions();
    }
    function initialize(){
		$this->addComponent(new Label($this->textv), 'linkName');
		$this->onClickSend('execute', $this);
    }
	function initializeDefaultView(&$view){
		$view->setTagName('a');
	}
	function initializeView(&$view){}
	function execute(){
		$this->proceed->call();
	}
	function setOnClickEvent(&$view){
		parent::setOnClickEvent($view);
		$view->setAttribute('onclick', $view->getAttribute('onclick').'var ev = getEvent(event); ev.returnValue=false;return false;');
	}

}
?>