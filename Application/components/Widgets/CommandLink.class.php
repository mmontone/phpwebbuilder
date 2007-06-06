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
		$vm = null;
		parent::Widget($vm);
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

    function printString() {
        return $this->debugPrintString();
    }

    function debugPrintString() {
        return $this->primPrintString($this->proceed->target->printString() . '->' . $this->proceed->method_name);
    }
}
?>