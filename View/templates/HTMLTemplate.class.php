<?php
class HTMLTemplate extends XMLNodeModificationsTracker {
	function & instantiate() {
		if (count($this->childNodes) != 1) {
			$tv = & new XMLNodeModificationsTracker();
			foreach ($this->childNodes as $h) {
				$t = & $this->duplicate($h);
				$tv->insert_in($t,$tv->nextNode++);
			}
		} else {
			$fc =& $this->first_child();
			$tv = & $this->duplicate($fc);
		}
		return $tv;
	}

	function &duplicate(&$fc){
		#@php5
		$n = null;
		$fc->parentNode =& $n;
		$tv = unserialize(serialize($fc));
		return $tv;
		//@#
		#@php4
		return $this->xml2template($fc);
		//@#

	}
	function renderEcho() {
		echo $this->renderNonEcho();
	}

	function renderNonEcho() {
		$fc =& $this->first_child();
		$tag_name= $fc->tagName;
		$this->getRealId();
		$fid = $this->getAttribute('fakeid');
		return "<script id=\"$fid\">0;</script>";
		//return "<$tag_name style=\"visibility:hidden\" id=\"$fid\">0;</$tag_name>";
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
				#@gencheck if ($xml->childNodes[0]==null) print_backtrace_and_exit();@#
				$ret = new XMLTextNode(Translator::Translate(trim($xml->childNodes[0]->data)));
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
		return $component->hasType($this->getAttribute("class"));
	}
	function isContainerForClass(& $component) {

		return $component->hasType($this->getAttribute("class"));
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