<?php
class HTMLTemplate extends XMLNodeModificationsTracker {
	function & instantiate() {
		if (count($this->childNodes) != 1) {
			$app =& Application::instance();
			$tv = & new XMLNodeModificationsTracker($app->page_renderer->defaultTag());
			foreach ($this->childNodes as $h) {
				$t = & $this->xml2template($h);
				$tv->insert_in($t,$tv->nextNode++);
			}
		} else {
			$tv = & $this->xml2template($this->first_child());
		}
		return $tv;
	}


	function renderEcho() {
		$this->getRealId();
		$fid = $this->getAttribute('fakeid');
		echo "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
	}

	function renderNonEcho() {
		$this->getRealId();
		$fid = $this->getAttribute('fakeid');
		return "<span style=\"visibility:hidden\" id=\"$fid\"></span>";
	}

	function getRealId(){
		$id =$this->parentNode->getRealId();
		$id.= CHILD_SEPARATOR.$this->getAttribute('class');
		$id.= CHILD_SEPARATOR.$this->getAttribute('simpleId');
		$this->attributes['fakeid'] =$id;
	}
	function getId(){
		$this->getRealId();
		return $this->getAttribute('fakeid');
	}
	function & xml2template(& $xml) {
		$tag =& $xml->tagName;
		$atts = $xml->attributes;
		switch ($tag){
			case 'template': $temp = & new HTMLTemplate($tag,$atts); break;
			case 'container': $temp = & new HTMLContainer($tag,$atts); break;
			case 'translated':
				$ret = new XMLTextNode(Translator::Translate($xml->childNodes[0]->data));
				return $ret;
				break;
			case '': $ret =& new XMLTextNode($xml->data);
					return $ret;
					break;
			default: $temp = & new XMLNodeModificationsTracker($tag,$atts);
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
		return is_a($component, $this->getAttribute("class"));
	}
	function isContainerForClass(& $component) {
		return is_a($component, $this->getAttribute("class"));
	}
	function &createCopy() {
		$c =& new HTMLContainer;
		return $c;
	}
	function isTemplate() {
		return true;
	}

	function getClass() {
		return strtolower($this->getAttribute('class'));
	}
}
?>