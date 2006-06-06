<?php
require_once dirname(__FILE__) . '/DOMXMLNode.class.php';

class XMLNode extends DOMXMLNode {
	var $controller;
	var $templates = array ();
	var $containers = array ();
	var $childById = array ();
	function XMLNode($tag_name = 'div', $attributes = array ()) {
		return parent :: DOMXMLNode($tag_name, $attributes);
	}

	function & createElement($tag_name, & $controller) {
		$element = & parent :: createElement($tag_name);
		$element->controller = & $controller;
		return $element;
	}
	/* Always access attributes through getAttribute */

	function setAttribute($name, $val) {
		$this->attributes[$name] = $val;
	}
	function setId($id) {
		$this->setAttribute('id', $id);
	}
	function getRealId() {
		if (($this->controller != null)) {
			$id = $this->controller->getId();
			$this->attributes['id'] =  $id;
			$this->attributes['name'] = $id;
			return $id;
		} else {
			return $this->parentNode->getRealId();
		}
	}
	function getId(){
		$this->getRealId();
		return $this->getAttribute('id');
	}
	function render(){
		ob_start();
		$this->renderEcho();
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}
	function renderEcho() {
		$this->getRealId();
		if ($this->getAttribute('id')!='' && !$this->controller) {
			return;
		}
		$cn =& $this->childNodes;
		echo implode('',array('<',$this->tagName));
		foreach ($this->attributes as $name => $val) {
			echo implode('' , array(' ', $name , '="' , $val, '"'));
		}
		if (count($cn) == 0) {
			echo '/>';
		} else {
			echo '>';
			$ks = array_keys($cn);
			foreach ($ks as $k) {
				$cn[$k]->renderEcho();
			}
			//echo implode(array("\n</".$this->tagName.'>'));
			echo implode(array('</'.$this->tagName.'>'));
		}
	}
	// For debugging
	function printString() {
		$this->getRealId();
		$attrs = "";
		foreach ($this->attributes as $name => $val) {
			$attrs .= ' ' . $name . '="' . $val . '"';
		}
		if (count($this->childNodes) == 0) {
			return "<$this->tagName $attrs />";
		}
		else {
			$childs = '';
			$ks = array_keys($this->childNodes);
			foreach ($ks as $k) {
				$childs .= $this->childNodes[$k]->printString();
			}
			$ret .= "\n<$this->tagName $attrs><children>$childs</children></$this->tagName>\n";
			return $ret;
		}
	}
	function & childrenWithId($id) {
		$i = strtolower($id);
		if (isset($this->childById[$i])){
			$new =& $this->childById[$i];
			$this->unsetChildWithId($i);
			return $new;
		} else {
			return null;
		}
	}
	function unsetChildWithId($id){
		if (isset($this->childById[$id])){
			unset($this->childById[$id]);
			if ($this->parentView){
				$this->parentView->unsetChildWithId($id);
			}
		}
	}
	function getTemplatesAndContainers() {
		$temp = array ();
		$cont = array ();
		$childId = array ();
		$cn = & $this->childNodes;
		$ks = array_keys($cn);
		foreach ($ks as $k) {
			$t = & $cn[$k];
			$t->getTemplatesAndContainers();
			if ($t->isTemplate()) {
				$temp[] = & $t;
				$cont[] = & $t;
			} else if (isset($t->attributes["id"])) {
				$childId[strtolower($t->attributes["id"])]=&$t;
			} else if ($t->isContainer()) {
				$cont[] = & $t;
			} else {
				$this->addTemplatesAndContainersChild($t);
			}
		}
		$this->addTemplatesAndContainers($temp, $cont, $childId);
	}
	function addTemplatesAndContainers(& $temp, & $cont, & $childId) {
		$t = & $this->templates;
		$c = & $this->containers;
		$i = & $this->childById;
		$ks1 = array_keys($childId);
		foreach ($ks1 as $k1) {
			$i[$k1] = & $childId[$k1];
		}
		$ks2 = array_keys($temp);
		foreach ($ks2 as $k2) {
			$t[] = & $temp[$k2];
		}
		$ks3 = array_keys($cont);
		foreach ($ks3 as $k3) {
			$c[] = & $cont[$k3];
		}
	}
	function addTemplatesAndContainersChild(& $v) {
		$this->addTemplatesAndContainers($v->templates, $v->containers, $v->childById);
	}
	function &templateForClass(& $component) {
		$res = array ();
		$ks = array_keys($this->templates);
		foreach ($ks as $k) {
			$t = & $this->templates[$k];
			if ($t->isTemplateForClass($component)) {
				return $t;
			}
		}
		return null;
	}
	function & containerForClass(& $component) {
		$res = array ();
		$ks = array_keys($this->containers);
		foreach ($ks as $k) {
			$t = & $this->containers[$k];
			if ($t->isContainerForClass($component)) {
				return $t;
			}
		}
		return null;
	}
	function isTemplateForClass(& $component) {
		return false;
	}
	function isContainerForClass(& $component) {
		return false;
	}
	function isContainer() {
		return false;
	}
	function isTemplate() {
		return false;
	}
	function hasId($id) {
		$b = ($this->attributes["id"] !== null && strcasecmp($this->attributes["id"], $id) == 0);
		return $b;
	}
	function checkTree() {
		assert($this->parentNode);
		foreach (array_keys($this->childNodes) as $i) {
			assert($this->childNodes[$i]->parentNode);
			$this->childNodes[$i]->checkTree();
		}
	}
}
?>