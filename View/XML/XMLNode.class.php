<?php

class XMLNode extends DOMXMLNode {
	var $controller;
	var $templates = array ();
	var $containers = array ();
	var $childById = array ();
	var $css_classes = array();
	var $cache;
	function XMLNode($tag_name = null, $attributes = array ()) {
		parent :: DOMXMLNode($tag_name, $attributes);
		$this->addCSSClasses();
	}
	function releaseAll(){
		$this->release();
		$cn =& $this->childNodes;
		foreach (array_keys($cn) as $k) $cn[$k]->releaseAll();
		$c = null;
		$v = null;
		$this->controller->view =& $v;
		$this->controller =& $c;
		$this->childNodes = array();
	}
	function addCSSClasses(){
		$c = $this->getAttribute('class');
		if ($c!=''){
			foreach(explode(' ',$c) as $css){
				$this->addCSSClass($css);
			}
		}
	}
	function addCSSClass($class) {
		$this->css_classes[$class] = $class;
		$this->setAttribute('class', implode(' ', $this->css_classes));
	}
	function removeCSSClasses() {
		$this->css_classes=array();
		$this->removeAttribute('class');
	}
	function removeCSSClass($class) {
		unset($this->css_classes[$class]);
		if (empty($this->css_classes)) {
			$this->removeAttribute('class');
		}
		else {
			$this->setAttribute('class', implode(' ', $this->css_classes));
		}
	}

	function & createElement($tag_name, & $controller) {
		$element = & parent :: createElement($tag_name);
		$element->controller = & $controller;
		return $element;
	}
	/* Always access attributes through getAttribute */

	function setId($id) {
		$this->setAttribute('id', $id);
	}
	function &getController(){
		if ($this->controller !== null) {
			return $this->controller;
		} else {
			return $this->parentNode->getController();
		}
	}
	function getRealId() {
		if ($this->controller!=null){
			return $this->attributes['id'];
		} else {
			//echo 'going up for id';
			return $this->parentNode->getRealId();
		}
		/*if (($this->controller != null)) {
			$id = $this->controller->getId();
			$this->attributes['id'] =  $id;
			return $id;
		} else {
				echo 'going up for id';
				if (!$this->parentNode) print_backtrace('no parent');
				return $this->parentNode->getRealId();
		}*/
	}
	function getId(){
		return $this->getAttribute('id');
	}
	function render(){
		/*if ($ok) {
			$ok = @ob_start();
			$this->renderEcho();
			$s = ob_get_contents();
			echo $s;
			ob_end_clean();
			return $s;
		} else {*/
			$out = $this->renderNonEcho();
			return $out;
		//}
	}

	function renderEcho() {
		$id =& $this->getId();
		if ($id!=null && !isset($this->controller)) {
			#@debugview
			{
				$this->addCSSclass('hiddencontainer');
				$this->appendChild(new XMLTextNode($id));
			} if (false) //@#
				return;
		}
		$cn =& $this->childNodes;
		$tn =& $this->tagName;
		echo '<',$tn;
		foreach ($this->attributes as $name => $val) {
			echo ' ', $name , '="' , $val, '"';
		}
		if (count($cn)==0) {
			echo ' />';
		} else {
			echo ' >';
			foreach (array_keys($cn) as $k) {
				$cn[$k]->renderEcho();
			}
			echo '</',$tn,'>';
		}
	}

	function renderNonEcho() {
		if ($this->cache === null){
			$out = '';
			$id = $this->getId();
			if ($id!=null && !$this->controller) {
				#@debugview
				{
					$this->addCSSclass('hiddencontainer');
					$this->appendChild(new XMLTextNode($id));
				} if (false) //@#
					return;
			}
			$tn = $this->tagName;
			$out .= '<'.$tn;


			foreach ($this->attributes as $name => $val) {
				$out .= implode('' , array(' ', $name , '="' , $val, '"'));
			}
			$cn =& $this->childNodes;
			if (count($cn) == 0) {
				$out .= '/>';
			} else {
				$out .= '>';
				foreach (array_keys($cn) as $k) {
					$out .= $cn[$k]->renderNonEcho();
				}
				//echo implode(array("\n</".$this->tagName.'>'));
				$out .= '</'.$tn.'>';
			}
			$this->cache = $out;
		}
		return $this->cache;
	}
	function flushCache(){
		if ($this->cache!==null && $this->parentNode!==null){
			$this->parentNode->flushCache();
		}
		$this->cache=null;
	}


	// For debugging
	function printString() {
		//$this->getRealId();
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
			$n = null;
			return $n;
		}
	}
	function unsetChildWithId($id){
		if (isset($this->childById[$id])){
			unset($this->childById[$id]);
			if ($this->parentNode){
				$this->parentNode->unsetChildWithId($id);
			}
		}
	}
	function getTemplatesAndContainers($addTemplates) {
		$temp = array ();
		$cont = array ();
		$childId = array ();
		$cn = & $this->childNodes;
		foreach (array_keys($cn) as $k) {
			$t = & $cn[$k];
			$t->getTemplatesAndContainers($addTemplates);
			if ($t->isTemplate()) {
				$temp[] = & $t;
				$cont[] = & $t;
			} else if (isset($t->attributes['id'])) {
				$childId[strtolower($t->attributes['id'])]=&$t;
			} else if ($t->isContainer()) {
				$cont[] = & $t;
			} else {
				$this->addTemplatesAndContainersChild($t, $addTemplates);
			}
		}
		//if (!$addTemplates) {$temp = array();}
		$this->addTemplatesAndContainers($temp, $cont, $childId);
	}
	function addTemplatesAndContainers(& $temp, & $cont, & $childId) {
		$t = & $this->templates;
		$c = & $this->containers;
		$i = & $this->childById;
		foreach (array_keys($childId) as $k1) {
			$i[$k1] = & $childId[$k1];
		}
		foreach (array_keys($temp) as $k2) {
			$class_temp = $temp[$k2]->getClass();
			foreach(get_subclasses_and_class($class_temp) as $class){
				if ((!isset($t[strtolower($class)])) || is_strict_subclass($class_temp, $t[strtolower($class)]->getClass())){
					$t[strtolower($class)] = & $temp[$k2];
				} else {
					//break;
				}
			}
		}
		foreach (array_keys($cont) as $k3) {
			$c[] = & $cont[$k3];
		}
	}
	function addTemplatesAndContainersChild(& $v, $addTemplates) {
		if ($addTemplates){
			$this->addTemplatesAndContainers($v->templates, $v->containers, $v->childById);
		} else {
			$this->addTemplatesAndContainers($arr=array(), $v->containers, $v->childById);
		}
	}
	function &templateForClass(& $component) {
		$ts =& $this->templates;
		foreach ($component->getTypes() as $m) {
			if (isset($ts[$m])){
				return $ts[$m];
			}
		}
		$n = null;
		return $n;
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
		$n=null;
		return $n;
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