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
    		$app =& Application::instance();
    		$pms = $app->urlManager->getBookmrkAndParams($bookmark);
    		$this->addComponent(new NavigationLink($pms['bookmark'], $name, $pms['params']));
    	}
    	$this->addComponent(new Label($arr[$i]));
    }
}
?>