<?php

class Bookmark {
	function launchIn(&$app, $params){}
	function launch($params){
		$app =& Application::instance();
		if ($this->checkPermissions($params)){
			$this->launchIn($app, $params);
		} else {
			$app->badUrl(substr(getClass($this),0,-8),$params);
		}
	}
	function checkPermissions($params){return true;}
}


class HomeBookmark extends Bookmark{
	function launchIn(&$app, $params){
		$app->component->start();
	}
}
?>