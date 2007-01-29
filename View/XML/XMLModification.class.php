<?php

class XMLNodeModification extends PWBObject{
	var $target;

	function XMLNodeModification(&$target) {
		parent::PWBObject();
		$this->target =& $target;
	}

	function visit(&$visitor, $params) {
		$visit_selector = 'visit' . getClass($this);
		return $visitor->$visit_selector($this, $params);
	}
	function willFlush(){return true;}
	function addChildMod($pos,&$mod){}
	function removeChildMod($pos){}
	function printTree(){
		return getClass($this);
	}
	function renderJsResponseCommand(){
		$xml = $this->renderAjaxResponseCommand();
		$xml = str_replace('"','\\"',$xml);
		$xml = str_replace("\n","\\n",$xml);
		echo
		"<script>
		var parser=new DOMParser();
		var str = \"<ajax>".$xml."</ajax>\";
  		var xmlDoc=	parser.parseFromString(str,'text/xml');
		window.frameElement.ownerDocument.window.updatePage(str, xmlDoc);</script>";
		flush();
	}

}

class ReplaceNodeXMLNodeModification extends XMLNodeModification {
	var $replacement;

	function ReplaceNodeXMLNodeModification(& $replacement, &$target) {
		parent::XMLNodeModification($target);
		$this->replacement = & $replacement;
	}
	function renderAjaxResponseCommand() {
		$xml = '<repn id="' . $this->target->getId() . '">';
		$xml .= $this->replacement->render();
		$xml .= '</repn>';
		return $xml;
	}
}


class BookmarkXMLNodeModification extends XMLNodeModification {
	var $hash;

	function BookmarkXMLNodeModification($hash) {
		parent::XMLNodeModification($n=null);
		$this->hash = $hash;
	}
	function renderAjaxResponseCommand() {
		$xml = '<bookmark hash="' . $this->hash. '"/>';
		return $xml;
	}
}

class ReplaceChildXMLNodeModification extends XMLNodeModification {
	var $child;
	var $replacement;
	function ReplaceChildXMLNodeModification(& $replacement, & $child, &$target) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
		$this->childId = $child->getId();
		$this->replacement = & $replacement;
	}
	function renderAjaxResponseCommand() {
		//$this->replacement->flushModifications();
		$xml = '<repn id="' . $this->childId . '">';
		$xml .= $this->replacement->render();
		$xml .= '</repn>';
		return $xml;
	}
	function apply_replace(&$elem){
		$this->replacement = & $elem;
	}
}

class NullXMLNodeModification extends XMLNodeModification {
	function renderAjaxResponseCommand() {}
	function willFlush(){return false;}
}

class ChildModificationsXMLNodeModification extends XMLNodeModification {
	var $modifications = array();
	function renderAjaxResponseCommand() {
		foreach (array_keys($this->modifications) as $i) {
			$mod =& $this->modifications[$i];
			$xml .= $mod->renderAjaxResponseCommand();
		}
		return $xml;
	}
	function addChildMod($pos,&$mod){
		//if ($this->modifications[$pos]) echo "replacing a ".getClass($this->modifications[$pos])." by ".getClass($mod). " in ".$pos; else echo "adding a ".getClass($mod). " in ".$pos;
		$this->modifications[$pos] =& $mod;
	}
	function removeChildMod($pos){
		//if ($this->modifications[$pos]) echo "removing a ".getClass($this->modifications[$pos])." in ".$pos;
		unset($this->modifications[$pos]);
	}
	function printTree(){
		$ret = getClass($this) . '{';
		foreach($this->modifications as $k=>$m){
			$ret .= $k.':'.$m->printTree();
		}
		return $ret.'}';
	}
	function renderJsResponseCommand() {
		foreach (array_keys($this->modifications) as $i) {
			$mod =& $this->modifications[$i];
			$mod->renderJsResponseCommand();
		}
	}
}


class InsertBeforeXMLNodeModification extends XMLNodeModification {
	var $old;
	var $new;
	function InsertBeforeXMLNodeModification(&$target, &$old, &$new){
		parent::XMLNodeModification($target);
		$this->old = & $old;
		$this->new = & $new;
	}
	function renderAjaxResponseCommand() {
		$xml = '<insert id="' . $this->old->getId() . '">';
		$xml .= $this->new->render();
		$xml .= '</insert>';
		return $xml;
	}
	function apply_replace(&$elem){
		$this->new = & $elem;
	}
}

class AppendChildXMLNodeModification extends XMLNodeModification {
	var $child;
	function AppendChildXMLNodeModification(&$target, & $child) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
	}
	function renderAjaxResponseCommand() {
		//$this->child->flushModifications();
		$xml = '<append id="' . $this->target->getId() . '">';
		$xml .= $this->child->render();
		$xml .= '</append>';
		return $xml;
	}
	function apply_replace(&$elem){
		$this->child = & $elem;
	}
}

class RemoveNodeXMLNodeModification extends XMLNodeModification {
	function RemoveNodeXMLNodeModification(&$target)  {
		parent::XMLNodeModification($target);
	}
	function renderAjaxResponseCommand() {
		$xml = '<rem id="' . $this->target->getId() . '" />';
		return $xml;
	}
}

class RemoveChildXMLNodeModification extends XMLNodeModification {
	var $child;
	function RemoveChildXMLNodeModification(&$target, & $child) {
		parent::XMLNodeModification($target);
		$this->child = & $child;
		$this->childId = $child->getId();
	}
	function renderAjaxResponseCommand() {
		$xml = '<rem id="' . $this->childId . '" />';
		return $xml;
	}
}

class SetAttributeXMLNodeModification extends XMLNodeModification {
	var $attribute;
	var $value;
	function SetAttributeXMLNodeModification(&$target, $attribute, $value) {
		parent::XMLNodeModification($target);
		$this->attribute = $attribute;
		$this->value = $value;
	}
	function renderAjaxResponseCommand() {
		$ret = '<setatt id="' . $this->target->getId() . '">';
		$ret.= '<att>' . $this->attribute . '</att>';
		$ret.= '<val>' . $this->value . '</val>';
		$ret.= '</setatt>';
		return $ret;
	}
}

class RemoveAttributeXMLNodeModification extends XMLNodeModification {
	var $attribute;
	function RemoveAttributeXMLNodeModification(&$target, $attribute) {
		parent::XMLNodeModification($target);
		$this->attribute = $attribute;
	}
	function renderAjaxResponseCommand() {
		$xml = '<rematt id="' . $this->target->getId() . '">';
		$xml .= '<att>' . $this->attribute . '</att>';
		$xml .= '</rematt>';
		return $xml;
	}
}
?>