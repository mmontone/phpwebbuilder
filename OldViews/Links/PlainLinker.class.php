<?php

require_once dirname(__FILE__) . '/ViewLinker.class.php';
class PlainLinker extends ViewLinker {
	function visitedPersistentCollection($persistent_collection) {
			return "<a href=\"".$this->makeLinkAddressPersistentCollection($persistent_collection, $this->action, $this->append)."\">".$this->text."</a>";
	}
	function visitedPersistentObject($obj) {   
   		return "<a href=\"".$this->makeLinkAddressPersistentObject($obj, $this->action, $this->append)."\">".$this->text."</a>";
	}
}
?>
