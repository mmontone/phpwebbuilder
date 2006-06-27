<?php

class WikiComponent extends Widget{
	var $wikitext;
    function WikiComponent($text) {
		$this->wikitext = $text;
		parent::Widget($vm='');
    }
    function initialize(){
    	$arr = split('(\[|\])',' '.$this->wikitext.' ');
    	for ($i=0,$max=count($arr)-1;$i<$max; $i+=2){
    		$this->addComponent(new Label($arr[$i]));
    		$l =& $arr[$i+1];
    		$ls = explode(' ', $l,2);
    		$bookmark = $ls[0];
    		$name = $ls[1];
    		if (strcasecmp('http://',substr($bookmark,7))==0){
	    		$this->addComponent(new Link($bookmark, $name));
    		} else if (strcasecmp('mailto:',substr($bookmark,7))==0){
				$this->addComponent(new Link($bookmark, $name));
    		} else {
	    		$app =& Application::instance();
	    		$pms = $app->urlManager->getBookmarkAndParams($bookmark);
	    		$this->addComponent(new NavigationLink($pms['bookmark'], $name, $pms['params']));
    		}
    	}
    	$this->addComponent(new Label($arr[$i]));
    }
}
?>