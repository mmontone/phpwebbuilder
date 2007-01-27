<?php
class SessionHandler{
	function setHooks(){
		if (defined('sessionHandler')){
			$sht = constant('sessionHandler');
		} else {
			$sht= 'PHP';
		}
		$shc = $sht.'SessionHandler';
		global $sessionHandlerInstance;
		$sessionHandlerInstance = new $shc;
		$sessionHandlerInstance->setSessionHooks();
	}
	function &Instance(){
		global $sessionHandlerInstance;
		return $sessionHandlerInstance;
	}
	function isStarted(){
		return true;
	}
	function setSessionHooks(){}
}
class PHPSessionHandler extends SessionHandler{}

?>