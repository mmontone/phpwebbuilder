<?

require_once(dirname(__FILE__) . "/../Action/HtmlTableSearchFieldView.class.php");
require_once("HtmlFormEditView.class.php");

class HtmlTableSearchView extends HtmlFormEditView {
	function fieldShowObjectFactory () {
		return new HtmlTableSearchFieldView;
	}
	function visitedPersistentCollection ($obj) {
		$view = new PersistentCollectionHtmlTableSearchView;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject ($obj) {
		$view = new PersistentObjectHtmlTableSearchView;
		$view->obj = $obj;
		return $view;
	}

}

class PersistentObjectHtmlTableSearchView extends HtmlTableSearchView  {
	var $conds;
	function searchJS(){
		return "";
	}

	function show () {
		/*$col = new PersistentCollection(get_class($this->obj));
		$read = new HTMLReadView;
		$read = $read->viewFor($col);
		$read->readForm($_REQUEST, &$error_msgs);*/
		$this->conds = unserialize(stripslashes($_REQUEST["conditions"]));

		/*$col->conditions;*/
		//print_r($this->conds);
		$ret .= $this->formHeader();
        $ret .= $this->formAppend();
        $ret .= "\n<center><table>" .
        		"<input type=\"hidden\" name=\"Controller\" value=\"SearchController\" />";
        $ret .= $this->fieldsForm(FALSE);

		//if ($obj->existsObject) {$but="Update";} else {$but="Add";};

		//$ret .= "\n<tr><td align=right colspan=2><input type=\"Submit\" name=\"Enviar\" value=\"$but\"></td></tr>";

		$ret .= "\n<tr>";

        $ret .= "\n</table></center>";
		$ret .= $this->formFooter();
		$ret .= "<td align=right colspan=2>" .
				"<a class=\"operation\" href='javascript: document.getElementById(\"" . $this->formId() . "\").submit()'>" .
						"<img class=\"operation\" title=\"Search\" src='" . icons_url . "stock_search.png'/>" .
				"</a> </td>" .
				"</tr>";

		return $ret;
	}
	function listHeader ($colview) {
		$ret = "\n<tr>";
		foreach($this->obj->allFields() as $index=>$field){
			if ($field->isIndex) {
				$showField = $this->fieldShowObject($field);
				$ret .= $showField->listHeader($colview);
			}
		}

		$ret .= "<td></td>";

		return $ret . "\n</tr>";
	}
	function selForm ($default) {
		$this->obj->load();
		$ret = "\n<option value=\"".$this->obj->getID()."\"";
		if ($this->obj->getID() == $default) {$ret .= " selected";}
		$ret .= ">";
		$ret .= $this->obj->indexValues();
		$ret .= "\n</option>";
		return $ret;
	}
	function listForm () {
		$ret = "\n<tr>";
		foreach($this->obj->allFields() as $index=>$field){
			$showField = $this->fieldShowObject($field);
			$ret .= $showField->listForm($this->obj);
       		}

		// Edit
		if (hasPermission($_SESSION[sitename]["Username"], $_SESSION[sitename]["Permisos"] , "Edit", get_class($this->obj), "")) {
		$ret .= "\n<td class=\"operation\"><a href=\"".$this->obj->formphp."?Action=Edit&ObjType=".get_class($this->obj)."&ObjID=".$this->obj->getId()."\"><img title=\"Edit\" src=\"". icons_url . "stock_edit.png\"/></td>";
		}
		// Delete
		// La funcion ask de javascript esta en StandardController. Si, es un asco, pero quiero que el tipo tenga algo descente y no tengo tiempo
		if (hasPermission($_SESSION[sitename]["Username"], $_SESSION[sitename]["Permisos"] , "Delete", get_class($this->obj), "")) {
			$ret .= "\n<td class=\"operation\"><a href=\"javascript: ask('delete', '".get_class($this->obj) ." ". $this->obj->indexValues() . "', '".$this->obj->formphp."?Action=Delete&ObjID=".$this->obj->getID()."&ObjType=".get_class($this->obj)."&Delete".$this->obj->getId()."=yes');\"><img title=\"Delete\" src=\"". icons_url . "stock_delete.png\"/></a></td>";
		}
		$ret .= "\n</tr";
		return $ret;
	}


/*	function formName() {
		return $this->obj->tableName() . $this->obj->getID();
	}*/
	function makelink($text) {
   		return "<a href=\"".$this->obj->formphp."?Action=Edit&ObjID=".$this->obj->getID()."&ObjType=".get_class($this->obj)."\">".$text."</a>";
	}
}

class PersistentCollectionHtmlTableSearchView extends HtmlTableSearchView  {
      function show () {
         $colec = $this->obj;
	 	 $ret .= $this->showElements();
         $ret .= "\n<a href=\"$colec->formphp?Action=Add&ObjType=$colec->dataType\">";

         return $ret;
      }
      function showElements() {
         $colec = $this->obj;

	 	 $ret = "\n<table>";

         $obj = new $colec->dataType;
		 $html = $this->viewFor($obj);
         $ret .= $html->listHeader($this);
         $objs = $colec->objects();
         if ($objs) {
         	foreach($objs as $index=>$object) {
	 	 		$html = $this->viewFor($object);
		 		$ret .= $html->listForm();
         	}
         }
         $ret .= "\n</table>";
		 if (hasPermission($_SESSION[sitename]["Username"], $_SESSION[sitename]["Permisos"] , "Add", get_class($this->obj), get_class($obj))) {
		    $ret .= "\n<a href=\"$colec->formphp?Action=Add&ObjType=$colec->dataType\">";
         	$ret .= "\n<img title=\"New\" src=\"". icons_url . "stock_new.png\"/></a>";
		 }
		 //$ret .= "<a title=\"Go back\" href=\"javascript: history.go(-1)\"><img title=\"Go back\" src=\"". icons_url . "stock_undo.png\"/></a>";

		  if (($this->obj->offset-$this->obj->limit) < 0) {
		  	$back_offset=0;
		  } else {
		  	$back_offset = $this->obj->offset-$this->obj->limit;
		  }
		  $ret .= "<a title=\"Go back\" href=\"" .$this->makeLinkAddress("Show",array("offset"=>$back_offset)) ."\"><img title=\"Go back\" src=\"". icons_url . "stock_left.png\"/></a>";
          $ret .= "<a href=\"". $this->makeLinkAddress("Show",array("offset"=>($this->obj->offset+$this->obj->limit))) . "\" ><img title=\"New\" src=\"". icons_url . "stock_right.png\"/></a>";

         return $ret;
      }

      function showObjects(&$linker) {
        return $this->showElements($linker);
      }
/*	  function readForm ($form) {
		$this->obj->dataType = $form["dataType"];
        if (isset($form["Action"]) && $form["Action"]=="Delete"){
          foreach ($this->obj->objects() as $index=>$object) {
	  	$html = $this->viewFor($object);
            	$html->readForm($form);
          }
        }
      }*/
      function asSelect ($name, $default, $void) {
        $ret = "\n<select name=\"$name\">";
        if ($void!=""){
        	$ret .= "<option value=\"0\">$void</option>";
        }
        $this->obj->limit=5000;
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlTableSearchView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "&nbsp;</select>";
        $obj = new $this->obj->dataType;
		$ret .= "<a href=\"".$obj->formphp."?ObjType=". get_class($obj) ."&Action=Add\"><img title=\"New\" src=\"". icons_url . "stock_new-16.png\"/></a>";
        return  $ret;
      }
      function userSelect ($name, $default, $void) {
        $ret = "\n<select name=\"$name\ onSelect=javascript:autenticate(this) ;>";
        if ($void!=""){
        	$ret .= "<option value=\"0\">$void</option>";
        }
        $this->obj->limit=5000;
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlTableSearchView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "&nbsp;</select>";
        $obj = new $this->obj->dataType;
		$ret .= "<a href=\"".$obj->formphp."?ObjType=". get_class($obj) ."&Action=Add\"><img title=\"New\" src=\"". icons_url . "stock_new-16.png\"/></a>";
        return  $ret;
      }
      
      function voidOption () {}
	function makeLinkAddress ($action, $append) {
		$link = new PlainLinker;
		return $link->makelinkAddressPersistentCollection($this, $action, $append);
	}

}

?>
