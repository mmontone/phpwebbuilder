<?php

class HistoryListener {
	var $token=0;
	var $links=array();
	function getToken(&$actionlink){
		$t = $this->token++;
		$actionlink->setToken($t);
		$this->links[$t]=&$actionlink;
	}
	function receivedToken($token){
		$this->links[$token]->execute();
	}
}
?>