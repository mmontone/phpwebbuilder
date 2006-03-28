<?php

require_once("ViewLinker.class.php");

class JsLinker extends ViewLinker {
/*	function visitedPersistentCollection($persistent_collection) {
			$html = new HtmlDivEditView;
			$html = $html->viewFor($persistent_collection);
			$size = $html->getWindowSize();
			return $this->script(get_class($persistent_collection).$persistent_collection->dataType)."<A HREF=\"#\" onClick=\"javascript:open".get_class($persistent_collection).$persistent_collection->dataType."('".$this->makeLinkAddressPersistentCollection($persistent_collection, $this->action,  $this->append)."','".get_class($persistent_collection).$persistent_collection->dataType."','width=".$size["width"].",height=".$size["heigth"].",menubar=no, status=no, titlebar=no,border=no')\">".$this->text."</a>";
	}
	function visitedPersistentObject($obj) {
			$html = new HtmlDivEditView;
			$html = $html->viewFor($obj);
			$size = $html->getWindowSize();	      		
			return $this->script($html->formName())."<A HREF=\"javascript:open".$html->formName()."('".
			$this->makeLinkAddressPersistentObject($obj, $this->action, $this->append)."','".$html->formName()."','width=".$size["width"].",height=".$size["heigth"].",menubar=no, status=no, titlebar=no,border=no')\">".$this->text."</a>";
	}*/
	function linkSubmit($formid) {
		return "<img class=\"operation\" onclick=\"javascript:linkSubmit('$formid') ;\" title=\"Save changes\" src=\"" . icons_url . "stock_save.png\"/>"; 
	}
	function  colecAddLink($dataType, $formphp){
	   return "\n<img onclick=\"javascript:colecAddLink('$dataType', '$formphp');\" title=\"New\" src=\"". icons_url . "stock_new.png\"/>";
	}
	function showColecNext($colecview, $append) {
		return "<img title=\"Next\" onclick=\"javascript:showColecNext('".print_r($colecview,TRUE)."', '".print_r($append, TRUE)."')\" src=\"". icons_url . "stock_right.png\"/>"; 
	}
	function showColecBack($colecview, $append) {
		return "<img title=\"Previous\" onclick=\"javascript:showColecBack('".print_r($colecview,TRUE)."', '".print_r($append, TRUE)."')\" src=\"". icons_url . "stock_left.png\"/>"; 
	}
	function showListHeader($colecview, $colname) {
		return "<b onclick=\"javascript:showListHeader('".print_r($colecview,TRUE)."', '$colname')\" >".$colname."</b>";
	}
	function showObjSearch ($colecview, $dataType){
		return "<img title=\"Search\" onclick=\"javascript:showObjSearch('".print_r($colecview,TRUE)."', '$dataType')\" src=\"". icons_url . "stock_search.png\"/>";
	}
	function showObjEdit($formphp, $dataType, $id){
		return "<img title=\"Edit\" onclick=\"javascript:showObjEdit('$formphp', '$dataType', '$id')\" src=\"". icons_url . "stock_edit.png\"/>";
	}
	function showObjDelete($formphp, $dataType, $id, $indexValues){
		return "<img title=\"Delete\" onclick=\"javascript:showObjDelete('$formphp', '$dataType', '$id', '$indexValues')\" src=\"". icons_url . "stock_delete.png\"/>";
	}
	/*function showSelectAdd ($formphp, $datatype) {
		return "<a href=\"javascript:showSelectAdd ('$formphp', '$datatype', this)\"><img title=\"New\" src=\"". icons_url . "stock_new-16.png\"/></a>";
	}*/
	function showSelectAdd ($formphp, $datatype) {
		return "<img title=\"New\" onclick=\"javascript:showSelectAdd ('$formphp', '$datatype', this)\" src=\"". icons_url . "stock_new-16.png\"/>";
	}
	function showObjAdd($formphp,  $dataType, $firstValue) {
		return "<img title=\"New\" onclick=\"javascript:showObjAdd('$formphp', '$dataType', '$field')\" src=\"". icons_url . "stock_new-16.png\"/>";
	}
	function linkCancel($formid) {
		return "<img class=\"operation\" onclick=\"javascript:linkCancel('$formid', this) ;\" title=\"Cancel\" src=\"" . icons_url . "stock_cancel.png\"/>"; 
	}
	function showLogin($formid, $objType) {
		$href="'Action.php?Controller=InsertedLoginController&amp;userType=$objType'";
		$script = "goAjax($href, callbackInsert, this)";
		return "<img class=\"operation\" onclick=\"javascript:$script ;\" title=\"Login\" src=\"" . icons_url . "log_in.png\"/>"; 
	}
	
}


class InsertedJsLinker extends JsLinker{
	function showSelectAdd ($formphp, $datatype) {
		return "<img title=\"New\" onclick=\"javascript:showSelectAdd('$formphp', '$datatype', this);\" src=\"". icons_url . "stock_new-16.png\"/>";
	}
	function linkSubmit($formid) {
		return "<img class=\"operation\" onclick=\"javascript:insertedLinkSubmit('$formid', this) ;\" title=\"Save changes\" src=\"" . icons_url . "stock_save.png\"/>"; 
	}
	function linkCancel($formid, $datatype) {
		return "<img class=\"operation\" onclick=\"javascript:insertedLinkCancel('$formid', this) ;\" title=\"Cancel\" src=\"" . icons_url . "stock_cancel.png\"/>"; 
	}
	function showLogin($formid) {
		$href='\'Action.php?Controller=InsertedLoginController\'';
		$script = "goAjax($href, callbackInsert, this)";
		return "<img class=\"operation\" onclick=\"javascript:$script ;\" title=\"Login\" src=\"" . icons_url . "log_in.png\"/>"; 
	}
}

?>
