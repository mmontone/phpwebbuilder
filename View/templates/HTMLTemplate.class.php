<?php
class HTMLTemplate extends XMLNodeModificationsTracker {
	function & instantiateFor(& $component) {
		if (count($this->childNodes) != 1) {
			$tv = & new XMLNodeModificationsTracker;
			foreach ($this->childNodes as $h) {
				$t = & $this->xml2template($h);
				$tv->insert_in($t,$tv->nextNode++);
			}
		} else {
			$tv = & $this->xml2template($this->first_child());
		}
		$component->setView($tv);
		return $tv;
	}


	function renderEcho() {
		$this->getRealId();
		$fid = $this->getAttribute('fakeid');
		echo "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
	}
	function getRealId(){
		$this->parentNode->getRealId();
		$id = $this->parentNode->getAttribute('id');
		$id.= '/'.$this->getAttribute('id');
		$id.= '/'.$this->getAttribute('class');
		$this->attributes['fakeid'] =$id;
	}
	function getId(){
		$this->getRealId();
		return $this->getAttribute('fakeid');
	}
	function & xml2template(& $xml) {
		$tag =& $xml->tagName;
		switch ($tag){
			case 'template': $temp = & new HTMLTemplate($tag,$xml->attributes); break;
			case 'container': $temp = & new HTMLContainer($tag,$xml->attributes); break;
			case 'translated':
				$text = $xml->childNodes[0]->data;
				$translator = translator;
				$translator =& new $translator;
				$translated_text = $translator->translate($text);
				return new XMLTextNode($translated_text);
				break;
			case '': return new XMLTextNode($xml->data); break;
			default: $temp = & new XMLNodeModificationsTracker($tag,$xml->attributes);
		}
		$cs =& $xml->childNodes;
		$i =& $temp->nextNode;
		$ks = array_keys($cs);
		foreach ($ks as $k) {
			$temp->insert_in($this->xml2template($cs[$k]),$i++);
		}
		return $temp;
	}
	function isTemplateForClass(& $component) {
		return is_a($component, $this->attributes["class"]);
	}
	function isContainerForClass(& $component) {
		return is_a($component, $this->attributes["class"]);
	}
	function createCopy() {
		return new HTMLContainer;
	}
	function isTemplate() {
		return true;
	}
}
?>