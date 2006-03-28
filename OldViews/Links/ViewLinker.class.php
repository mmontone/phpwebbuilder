<?php

class ViewLinker {
	var $append;
	var $action;
	var $text;
	function makelink($obj, $text, $action, $append) {   
		$this->text = $text;
		$this->append = $append;
		$this->action = $action;
		return $obj->obj->visit($this);
	}
	function makeLinkAddressPersistentCollection($persistent_collection, $action, $appends){
               $appends = array_merge($persistent_collection->obj->toPlainArray(), $appends);
               $appends["ObjType"] = $persistent_collection->obj->dataType;
               $appends["Controller"] = "ShowController";
               unset($appends["dataType"]);
               $ret="";
               foreach ($appends as $i=>$a) { 
                       $ret .= "&".$i."=".$a;
               }
               return $persistent_collection->formphp."?Action=".$action.
                       $ret;
	}
	function makeLinkAddressPersistentObject($obj, $action, $append){
		$append .= "&Controller=ShowController";
		return $obj->formphp."?Action=".$action."&ObjID=".$obj->getID()."&ObjType=".get_class($obj).$append;
	}
	function linkCancel($obj){
	}
}
?>
