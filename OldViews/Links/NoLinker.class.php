<?php

require_once("ViewLinker.class.php");

class NoLinker extends ViewLinker {
	function linkSubmit($formid) {
		return ""; 
	}
	function  colecAddLink($dataType, $formphp){
	   return "";
	}
	function showColec($colecview, $append, $text) {
		return "";
	}
	function showColecNext($colecview, $append) {
		return ""; 
	}
	function showColecBack($colecview, $append) {
		return ""; 
	}
	function showListHeader($colecview, $colname) {
		return ""; 
	}
	function showObjSearch ($colecview, $dataType){
		return "";
	}
	function showObjEdit($formphp, $dataType, $id){
		return "";
	}
	function showObjDelete($formphp, $dataType, $id, $indexValues){
		return "";
	}
	
	function showSelectAdd ($formphp, $datatype) {
		return "";
	}
	function showObjAdd($formphp,  $dataType, $firstValue) {
		return "";	
	}
}

?>
