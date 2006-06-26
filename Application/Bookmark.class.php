<?php

class Bookmark {
	function launchIn(&$app, $params){}
}

class HomeBookmark extends Bookmark{
	function launchIn(&$app, $params){
		$app->component->start();
	}
}
?>