<?php

#@preprocessor
Compiler::usesClass(__FILE__,constant('sessionHandler').'SessionHandler');
//@#


class SessionHandler{
	function setHooks(){
		if (defined('sessionHandler')){
			$sht = constant('sessionHandler');
		} else {
			$sht= 'PHP';
		}
		$shc = $sht.'SessionHandler';
		$GLOBALS['sessionHandlerInstance'] =& new $shc;
		$GLOBALS['sessionHandlerInstance']->setSessionHooks();
	}
	function &Instance(){
		return $GLOBALS['sessionHandlerInstance'];
	}
	function isStarted(){
		return true;
	}
	function setSessionHooks(){}
}
class PHPSessionHandler extends SessionHandler{}

?>