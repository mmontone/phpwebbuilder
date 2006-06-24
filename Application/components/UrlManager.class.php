<?php

class UrlManager extends PWBObject{
	var $application;
    function UrlManager(&$app) {
    	$this->application =& $app;
    }
    function goBack(){
    	$c =& $this->application->commands->pop();
    	$c->revert();
    }
    function goToUrl($url){}
}
?>