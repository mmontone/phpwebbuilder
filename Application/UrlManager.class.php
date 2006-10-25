<?php

class UrlManager extends PWBObject{
	var $actUrl='Home';
	var $prevUrl='Home';
	var $application;
    function UrlManager(&$app) {
    	$this->application =& $app;
    }
    function goBack(){
    	$c =& $this->application->commands->pop();
    	$c->revert();
    }
    function resetUrl(){
		$this->setUrl($this->prevUrl);
    }
    function goToUrl($url){
    	$pms = $this->getBookmarkAndParams($url);
    	$this->navigate($pms['bookmark'], $pms['params']);
    }
    function getBookmarkAndParams($url){
    	$urls = explode('|', $url);
    	$bm = $urls[0];
    	$params=array();
    	array_shift($urls);
    	foreach($urls as $u){
    		$temp = explode('=', $u,2);
    		$params [$temp[0]] = $temp[1];
    	}
    	$ret ['bookmark'] = $bm;
    	$ret ['params'] = $params;
    	return $ret;
    }
    function setUrl($url){
		$this->prevUrl = $this->actUrl;
    	$this->actUrl = $url;
    	$this->application->wholeView->toFlush = &
    		new BookmarkXMLNodeModification($this->actUrl);
   		$this->application->wholeView->modifications['bookmark'] = & $this->application->wholeView->toFlush;
    }
    function navigate($bookmark, $params){
    	$this->setUrl($this->setBookmarkTarget($bookmark, $params));
    	$bmc = $bookmark.'Bookmark';
    	if (class_exists($bmc)){
	    	$bm =& new $bmc;
	    	$bm->launch($params);
    	} else {
    		$this->application->badUrl($bookmark, $params);
    	}
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