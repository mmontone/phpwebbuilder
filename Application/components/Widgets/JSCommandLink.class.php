<?php

class JSCommandLink extends Widget {
	var $text;
	var $target;

    function JSCommandLink($params) {
		$this->text =& $params['text'];
    	$this->target =& $params['target'];

    	parent::Widget($vh = null);
    }
	//TODO Remove view
	function setEvents(& $view) {}
    function initialize(){
		$this->addComponent(new Label($this->text), 'linkName');
		$this->addComponent($this->target, 'linkTarget');
    }
}

?>