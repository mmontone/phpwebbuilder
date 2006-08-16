<?php

class WikiComponent extends Widget{
	var $wikitext;
    function WikiComponent($text) {
		$this->wikitext = $text;
		parent::Widget($vm='');
    }
    function initialize(){
    	$arr = split("\n",' '.$this->wikitext.' ');
    	foreach($arr as $p){
    	  	$this->addComponent($this->addParag($p));
    	}
    }
    function &addParag($p) {
    	$arr = split('(\[|\])',' '.$p.' ');
    	$comp =& new Component;
    	for ($i=0,$max=count($arr)-1;$i<$max; $i+=2){
    		$comp->addComponent(new Label($arr[$i]));
    		$comp->addComponent($this->addLink($arr[$i+1]));
    	}
    	$comp->addComponent(new Label($arr[$i]));
    	return $comp;
    }
    function &addLink(&$l){
		$ls = explode(' ', $l,2);
		$bookmark = $ls[0];
		$name = $ls[1];
		if (strcasecmp('http://',substr($bookmark,0,7))==0){
    		return new Link($bookmark, $name);
		} else if (strcasecmp('mailto:',substr($bookmark,0,7))==0){
			return new Link($bookmark, $name);
		} else {
    		$app =& Application::instance();
    		$pms = $app->urlManager->getBookmarkAndParams($bookmark);
    		return new NavigationLink($pms['bookmark'], $name, $pms['params']);
		}
    }
}
?>