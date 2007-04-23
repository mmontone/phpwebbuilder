<?php

class GoBackLink extends NavigationLink{

    function GoBackLink($name) {
    	parent::NavigationLink('',$name,$p = array());
    }
	function setOnClickEvent(){
		$this->events->atPut('onclick', $a=array('onclick', 'history.back();return false;'));
	}
}
?>