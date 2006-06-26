<?php

class UrlManager extends PWBObject{
	var $actUrl="Home";
	var $application;
    function UrlManager(&$app) {
    	$this->application =& $app;
    }
    function goBack(){
    	$c =& $this->application->commands->pop();
    	$c->revert();
    }
    function goToUrl($url){
    	$urls = split('\|', $url);
    	$bm = $urls[0];
    	$params=array();
    	array_shift($urls);
    	foreach($urls as $u){
    		$temp = split('=', $u);
    		$params [$temp[0]] = $temp[1];
    	}
    	$this->navigate($bm, $params);
    }
    function navigate($bookmark, $params){
    	$this->actUrl = $this->setBookmarkTarget($bookmark, $params);
    	$this->application->wholeView->modifications[] = &
    		new BookmarkXMLNodeModification($this->actUrl);
    	$bmc = $bookmark.'Bookmark';
    	$bm =& new $bmc;
    	$bm->launchIn($this->application, $params);
    }
	function setLinkTarget($bookmark, $params){
		return 'Action.php?app='.getClass($this->application).'&bookmark='.$this->setBookmarkTarget($bookmark, $params);
	}
	function setBookmarkTarget($bookmark, $params=array()){
		$pss =array($bookmark);
		foreach($params as $n=>$v) $pss []= $n .'='. $v;
		$ps = implode('|',$pss);
		return $ps;
	}
}
?>