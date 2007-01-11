<?php

class CommandLink extends Widget{
	var $proceed;
	var $revert;
	var $textv;
    function CommandLink($params) {
		$this->proceed =& $params['proceedFunction'];
		#@typecheck $this->proceed:FunctionObject#@
		$this->revert =& $params['revertFunction'];
		if (is_object($params['text'])) {
			$this->textv =& $params['text'];
		} else {
			$this->textv =& new ValueHolder($params['text']);
		}

		parent::Widget($vm = null);
    }
    function checkAddingPermissions(){
		return $this->proceed->hasPermissions();
    }
	function setEvents() {}
    function initialize(){
		$this->addComponent(new Text($this->textv), 'linkName');
		$this->onClickSend('execute', $this);
    }
	function execute(){
		$this->proceed->execute();
	}
	//TODO Remove view
	function setOnClickEvent(){
		parent::setOnClickEvent();
		$oc = $this->events->at('onclick');
		$this->events->atPut('onclick', $a=array('onclick', $oc[1].';return false;'));
	}

}
?>