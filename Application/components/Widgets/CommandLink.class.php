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
	function setEvents() {}
    function initialize(){
		$this->addComponent(new Label($this->textv), 'linkName');
		$this->onClickSend('execute', $this);
    }
	function execute(){
		$this->proceed->call();
	}
	//TODO Remove view
	function setOnClickEvent(){
		parent::setOnClickEvent();
		$oc = $this->events->at('onclick');
		$this->events->atPut('onclick', $a=array('onclick', $oc[1].'var ev = getEvent(event); ev.returnValue=false;return false;'));
	}

}
?>