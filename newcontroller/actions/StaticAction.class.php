<?php

/*
class StaticActionRenderer
{
    var $text;
    var $append;
    var $action;

    function StaticActionRenderer(&$view) {
        parent::ActionRenderer($view);
    }

    function makelink($obj, $text, $action, $append) {
        $this->text = $text;
        $this->append = $append;
        $this->action = $action;
        return $obj->obj->visit($this);
    }

    function makeLinkAddressPersistentCollection($sqlcolec, $action, $appends){
               $appends = array_merge($sqlcolec->obj->toPlainArray(), $appends);
               $ret="";
               foreach ($appends as $i=>$a) {
                       $ret .= "&".$i."=".$a;
               }
               return $sqlcolec->formphp."?Action=".$action.
                       $ret;
    }

    function makeLinkAddressPersistentObject($obj, $action, $append, &$html){
        return $obj->formphp."?Action=".$action."&ObjID=".$obj->getID()."&ObjType=".get_class($obj).$append;
    }

	function linkSubmit($formid, &$html) {
	   $this->view->renderSaveLink("javascript: document.getElementById(%27" . $formid . "%27).submit()",$html);
	}

	function renderAddCollectionElementLink(&$collection, &$html) {
	   $this->view->renderAddLink("AddCollectionElement.php?CollectionID=ObjType=$collection->dataType", $html);
	}

	function showColecNext($colecview, $append, &$html) {
		$this->view->renderNextLink($this->makelinkAddressPersistentCollection($colecview, "Show", $append), $html);
	}

    function showColecBack($colecview, $append, &$html) {
		$this->view->renderBackLink($this->makelinkAddressPersistentCollection($colecview, "Show", $append), $html);
	}

    function showListHeader($colecview, $colname, &$html) {
		return "<a href=\"". $this->makeLinkAddressPersistentCollection($colecview, "Show", array("order"=> "%20ORDER%20BY%20".$colname)) ."\">".
                               "<b>".$colname."</b>" .
                               "</a>";
	}
	function showObjSearch ($colecview, $dataType, &$html){
	   $this->view->renderSearchLink($this->makeLinkAddressPersistentCollection($colecview, "Add",array("ViewType"=>"HTMLTableSearchView", "ObjType"=>$dataType)), $html);
	}

	function showObjEdit($formphp, $dataType, $id, &$html){
		$this->view->renderEditLink($formphp."?Action=Edit&ObjType=".$dataType."&ObjID=".$id, $html);
    }

	function showObjDelete($formphp, $dataType, $id, $indexValues, &$html){
		$this->view->renderDeleteLink("'javascript: ask('delete', '".$dataType ." ". $indexValues . "', '".$formphp."?Action=Delete&ObjID=".$id."&ObjType=".$dataType."&Delete".$id."=yes'", $html);
	}

	function showSelectAdd ($formphp, $datatype, &$html) {
		$this->view->renderAddChildLink($formphp."?ObjType=". $datatype ."&Action=Add", $html);
	}

    function showObjAdd($formphp,  $dataType, $firstValue, &$html) {
	   $this->view->renderAddLink("$formphp?Action=Add&". $firstValue ."&ObjType=$dataType", $html);
	}
}
*/
?>
