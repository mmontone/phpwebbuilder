<?php

class GoBackLink extends NavigationLink{

    function GoBackLink($name) {
    	parent::NavigationLink('',$name,$p = array());
    }
    	//TODO Remove view
	function setOnClickEvent(&$view){
		$view->setAttribute('onclick', 'history.back();return false;');
	}
}
?>