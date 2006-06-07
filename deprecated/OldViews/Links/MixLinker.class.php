<?php

require_once dirname(__FILE__) . "/ViewLinker.class.php";

class MixLinker extends ViewLinker {
	function linkSubmit($formid) {
		return "<a class=\"operation\" " . "href=\"javascript: callAction(%27ControllerSubmit%27)\"" ." >" .
						"<img class=\"operation\" title=\"Save changes\" src=\"" . icons_url . "stock_save.png\"/></a>";
	}

    function linkCancel($formid, $obj_type) {
        return "<a title=\"Cancel\" href=\"Action.php?Controller=ShowController&Action=List&ObjType=$obj_type\"><img class=\"operation\" title=\"Cancel\" src=\"" . icons_url . "stock_cancel.png\"/></a>";

    }
	function  colecAddLink($dataType, $formphp){
	   return "\n<a href=\"$formphp?Controller=ShowController&Action=Add&ObjType=$dataType\">\n<img title=\"New\" src=\"". icons_url . "stock_new.png\"/></a>";
	}
	function showColec($colecview, $append, $text) {
		return "<a href=\"" .$this->makelinkAddressPersistentCollection($colecview, "List", $append)."\">".$text."</a>";
	}
	function showColecNext($colecview, $append) {
		return $this->showColec($colecview, $append, "<img title=\"Next\" src=\"". icons_url . "stock_right.png\"/>");
	}
	function showColecBack($colecview, $append) {
		return $this->showColec($colecview, $append, "<img title=\"Previous\" src=\"". icons_url . "stock_left.png\"/>");
	}
	function showListHeader($colecview, $colname) {
		return "<a href=\"". $this->makeLinkAddressPersistentCollection($colecview, "List", array("order"=> "%20ORDER%20BY%20".$colname)) ."\">".
                               "<b>".$colname."</b>" .
                               "</a>";
	}
	function showObjSearch ($colecview, $dataType){
		return "<a href=\"". $this->makeLinkAddressPersistentCollection($colecview, "Add",array("ViewType"=>"HTMLTableSearchView", "ObjType"=>$dataType)) . "\" ><img title=\"Search\" src=\"". icons_url . "stock_search.png\"/></a>";
	}
	function showObjEdit($formphp, $dataType, $id){
		return "<a href=\"".$formphp."?Controller=ShowController&Action=Edit&ObjType=".$dataType."&ObjID=".$id."\"><img title=\"Edit\" src=\"". icons_url . "stock_edit.png\"/>";
	}
	function showObjDelete($formphp, $dataType, $id, $indexValues){
		return "<a href=\"javascript: ask('delete', '".$dataType ." ". $indexValues . "', '".$formphp."?Controller=ShowController&Action=Delete&ObjID=".$id."&ObjType=".$dataType."&Delete".$id."=yes');\"><img title=\"Delete\" src=\"". icons_url . "stock_delete.png\"/></a>";
	}

	function showSelectAdd ($formphp, $datatype) {
		return "<a href=\"".$formphp."?Controller=ShowController&ObjType=". $datatype ."&Action=Add\"><img title=\"New\" src=\"". icons_url . "stock_new-16.png\"/></a>";
	}
	function showObjAdd($formphp,  $dataType, $firstValue) {
	return "\n<a href=\"$formphp?Controller=ShowController&Action=Add&". $firstValue ."&ObjType=$dataType\">" .
			 "\n<img title=\"New\" src=\"". icons_url . "stock_new.png\"/></a>";
	}
}

?>
