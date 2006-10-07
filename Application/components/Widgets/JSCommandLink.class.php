<?php

class JSCommandLink extends Widget {
	var $text;
	var $target;

    function JSCommandLink($params) {
		$this->text =& $params['text'];
    	$this->target =& $params['target'];

    	parent::Widget($vh = null);
    }

	function setEvents(& $view) {}
    function initialize(){
		$this->addComponent(new Label($this->text), 'linkName');
		$this->addComponent($this->target, 'linkTarget');
    }

	function initializeDefaultView(&$view){
		$view->setTagName('a');
		$fun =& $this->target->getMainFunction();
		$view->setAttribute('onClick', "javascript:$fun();");
	}
	function initializeView(&$view){}
}

?>