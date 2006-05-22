<?php

require_once 'ObjectComponent.class.php';

class ShowObjectComponent extends ObjectComponent {
    function initialize(){
    	$this->factory =& new ShowComponentFactory;
		parent::initialize();
    }
}
?>