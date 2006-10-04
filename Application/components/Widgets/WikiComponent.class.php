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
    	$comp =& new Component;
    	/* First, we just echo the {{text}} parts */
    	$arrHTML = split('(\{\{|\}\})',' '.$p.' ');
    	for ($i=0,$max=count($arrHTML)-1;$i<$max; $i+=2){
	    	$this->addNonHTML($comp, $arrHTML[$i]);
	    	$comp->addComponent(new HTML($arrHTML[$i+1]));
    	}
    	$this->addNonHTML($comp, $arrHTML[$i]);
    	return $comp;
    }
    function addNonHTML(&$comp, $text){
    	$this->addLinks($comp, $text);
    }
    function addLinks(&$comp, $text){
		/* Here, we parse the links */
		$arr = split('(\[|\])',' '.$text.' ');
    	for ($i=0,$max=count($arr)-1;$i<$max; $i+=2){
    		$comp->addComponent(new HTML($this->applyTextModifications($arr[$i])));
    		$comp->addComponent($this->addLink($arr[$i+1]));
    	}
    	$comp->addComponent(new HTML($this->applyTextModifications($arr[$i])));
    }
    function applyTextModifications($text){
    	$text = toAjax($text);
    	$text = ereg_replace('\*([^*]*)\*', '<b>\1</b>', $text);
    	return $text;
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
    		$nl =& new NavigationLink($pms['bookmark'], $name, $pms['params']);
    		if ($nl->checkAddingPermissions()) {
    			return $nl;
    		} else {
    			return new Label($name);
    		}

		}
    }
}
?>