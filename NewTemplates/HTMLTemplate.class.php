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


	function render() {
		$this->getRealId();
		$fid = $this->getAttribute('fakeid');
		return "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
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
		if (strcasecmp(get_class($xml), 'XMLTextNode') == 0 ||
			strcasecmp(get_class($xml), 'HTMLTextNode') == 0) {
			$tn = & new XMLTextNode($xml->data);
			return $tn;
		} else
			if (strcasecmp($xml->tagName, 'template') == 0) {
				$temp = & new HTMLTemplate;
			} else
				if (strcasecmp($xml->tagName, 'container') == 0) {
					$temp = & new HTMLContainer;
				} else {
					$temp = & new XMLNodeModificationsTracker;
				}
		foreach ($xml->childNodes as $c) {
			$temp->insert_in($this->xml2template($c),$temp->nextNode++);
		}
		$temp->attributes = $xml->attributes;
		$temp->tagName = $xml->tagName;
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