<?php

class Bookmark {
	function launchIn(&$app, $params){}
	function launch(&$win,$params){
		if ($this->checkPermissions($params)){
			$this->launchIn($win, $params);
		} else {
			$win->badUrl(substr(getClass($this),0,-8),$params);
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