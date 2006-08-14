<?php
class SessionHandler{
	function setHooks(){
		if (defined('sessionHandler')){
			$sht = constant('sessionHandler');
		} else {
			$sht= 'PHP';
		}
		$shc = $sht.'SessionHandler';
		$sh =& new $shc;
		$sh->setSessionHooks();
	}
	function setSessionHooks(){}
}
class PHPSessionHandler extends SessionHandler{}

class MMSessionHandler extends SessionHandler{}

?>