<?php 

class GoBackLink extends NavigationLink{

    function GoBackLink($name) {
    	parent::NavigationLink('',$name,$p = array());
    }
	function setOnClickEvent(&$view){
		$view->setAttribute('onclick', 'history.back();return false;');
	}
}
?>