<?php

class XMLNode extends DOMXMLNode {
	var $controller;
	var $templates = array ();
	var $containers = array ();
	var $childById = array ();
	var $css_classes = array();

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
			$csss = explode(' ',$c);
			foreach($csss as $css){
				$this->addCSSClass($css);
			}
		}
	}
	function addCSSClass($class) {
		$this->css_classes[$class] = $class;
		$this->setAttribute('class', implode(' ', $this->css_classes));
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

	function setAttribute($name, $val) {
		$this->attributes[$name] = $val;
	}
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
		if (($this->controller != null)) {
			$id = $this->controller->getId();
			$this->attributes['id'] =  $id;
			return $id;
		} else {
				if (!$this->parentNode) print_backtrace('no parent');
				return $this->parentNode->getRealId();
		}
	}
	function getId(){
		$this->getRealId();
		return $this->getAttribute('id');
	}
	function render(){

		/*$ok = @ob_start();
		if ($ok) {
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
		$id = $this->getId();
		if ($this->getAttribute('id')!='' && !$this->controller) {
			if (defined('debugview') and constant('debugview')=='1') {
				$this->addCSSclass('hiddencontainer');
				$this->appendChild(new XMLTextNode($id));
			} else {
				return;
			}
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

	function renderNonEcho() {
		$out = '';
		$id = $this->getId();
		if ($this->getAttribute('id')!='' && !$this->controller) {
			if (defined('debugview') and constant('debugview')=='1') {
				$this->addCSSclass('hiddencontainer');
				$this->appendChild(new XMLTextNode($id));
			} else {
				return;
			}
		}
		$out .= implode('',array('<',$this->tagName));


		foreach ($this->attributes as $name => $val) {
			$out .= implode('' , array(' ', $name , '="' , $val, '"'));
		}
		$cn =& $this->childNodes;
		if (count($cn) == 0) {
			$out .= '/>';
		} else {
			$out .= '>';
			$ks = array_keys($cn);
			foreach ($ks as $k) {
				$out .= $cn[$k]->renderNonEcho();
			}
			//echo implode(array("\n</".$this->tagName.'>'));
			$out .= implode(array('</'.$this->tagName.'>'));
		}

		return $out;
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
	function getTemplatesAndContainers() {
		$temp = array ();
		$cont = array ();
		$childId = array ();
		$cn = & $this->childNodes;
		foreach (array_keys($cn) as $k) {
			$t = & $cn[$k];
			$t->getTemplatesAndContainers();
			if ($t->isTemplate()) {
				$temp[] = & $t;
				$cont[] = & $t;
			} else if (isset($t->attributes['id'])) {
				$childId[strtolower($t->attributes['id'])]=&$t;
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
		foreach (array_keys($childId) as $k1) {
			$i[$k1] = & $childId[$k1];
		}
		foreach (array_keys($temp) as $k2) {
			$t[] = & $temp[$k2];
		}
		foreach (array_keys($cont) as $k3) {
			$c[] = & $cont[$k3];
		}
	}
	function addTemplatesAndContainersChild(& $v) {
		$this->addTemplatesAndContainers($v->templates, $v->containers, $v->childById);
	}
	function &templateForClass(& $component) {
		/*$res = array ();
		$ks = array_keys($this->templates);
		foreach ($ks as $k) {
			$t = & $this->templates[$k];
			if ($t->isTemplateForClass($component)) {
				return $t;
			}
		}
		$n = null;
		return $n;*/
		$templates =& new Collection();
		$templates->addAll($this->templates);
		$ts =& $templates->filter(lambda('&$template', 'return $template->isTemplateForClass($component);', get_defined_vars()));
		if (!$ts->isEmpty()) {
			$t = $ts->first();
			$es =& $ts->elements();
			foreach(array_keys($es) as $k){
				$tt =& $es[$k];
				if (in_array($t->getClass(), get_superclasses($tt->getClass()))) {
					$t =& $tt;
				}
			}

			return $t;
		}
		else {
			$n = null;
			return $n;
		}
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