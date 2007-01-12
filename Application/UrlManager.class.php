<?php

class UrlManager extends PWBObject{
	var $actUrl='Home';
	var $prevUrl='Home';
	var $application;
    function UrlManager(&$app) {
    	parent::PWBObject();
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
    	$this->navigate($pms['bm'], $pms['params']);
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
    	$ret ['bm'] = $bm;
    	$ret ['params'] = $params;
    	return $ret;
    }
    function setUrl($url){
		$this->prevUrl = $this->actUrl;
    	$this->actUrl = $url;
    	$this->application->wholeView->addChildMod('bm', new BookmarkXMLNodeModification($this->actUrl));
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
		return 'Action.php?'.(isset($_REQUEST['app'])?'app='.$_REQUEST['app']:'').'&bm='.$this->setBookmarkTarget($bookmark, $params);
	}

	function setBookmarkTarget($bookmark, $params=array()){
		$pss =array($bookmark);
		foreach($params as $n=>$v) $pss []= $n .'='. $v;
		$ps = implode('|',$pss);
		return $ps;
	}
}
?>