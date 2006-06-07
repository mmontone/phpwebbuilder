<?

require_once(dirname(__FILE__) . "/../Action/HtmlEditFieldView.class.php");
require_once(dirname(__FILE__) . "/../Structure/HtmlFormEditView.class.php");

class HtmlDivEditView extends HtmlFormEditView  {
	function fieldShowObjectFactory () {
		return new HtmlEditFieldView;
	}
	function visitedPersistentCollection ($obj) {
		$view = new PersistentCollectionHtmlDivEditView;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject ($obj) {
		$view = new PersistentObjectHtmlDivEditView;
		$view->obj = $obj;
		return $view;
	}
}

class PersistentObjectHtmlDivEditView extends HtmlDivEditView  {
	function showFields($linker, $fields) {
        $ret = "\n   <div class=\"form\">\n      <div id=\"".$this->formName()."\">";
		$ret .= $this->formHeader();
		$ret .= $this->formAppend();
		$ret .= $this->fieldsForm($linker, $fields, TRUE);
		$ret .= "\n   <div class=\"formbutton\">\n      <div id=\"".$this->formName()."formbutton\">" .
				$linker->linkSubmit($this->formId()) .
				"</div></div>";
		$ret .= $this->formFooter();
        $ret .= "\n      </div>\n   </div>";
		return $ret;
	}
	function listHeader ($colview) {
		$ret = "\n   <div class=\"listHeader\"> ";
		foreach($this->obj->allFields() as $index=>$field) {
			if ($field->isIndex) {
				$ret .= "\n         <div class=\"listHeaderCell\"> ";
				$ret .= $this->listHeaderField($field, $colview);
				$ret .= "\n         </div /*listHeaderCell*/>";
			}
		}
		$ret .= "\n      <div id=\"DeletelistHeader\"><h2>Delete</h2>\n      </div>";
		return $ret . "\n   </Div /*listHeader*/>";
	}
	function listHeaderField($field, $colview) {
		return "\n      <div id=\"".$field->field->colName."listHeader\">\n         " .
				"<a href=\"". $this->makeLinkAddress("Show", "&order=%20ORDER%20BY%20".$field->colName) ."\"><h2>".$field->colName. "</h2>\n      </a></div>";
	}

	function listForm () {
		$ret = "\n      <div class=\"listObjectLine\"> ";
		foreach($this->obj->allFields() as $index=>$field){
			if ($field->isIndex) {
				/*$showField = $this->fieldShowObject($field);*/
				$ret .= "\n         <div class=\"listObjectCell\"> ";
				$ret .= $this->listFormField($field);
				$ret .= "\n         </div>";
			}
		}
		if (hasPermission($_SESSION[sitename]["Username"], $_SESSION[sitename]["Permisos"] , "Edit", get_class($this->obj), "")) { 
		$ret .= "\n<div class=\"operation\"><a href=\"".$this->obj->formphp."?Action=Edit&ObjType=".get_class($this->obj)."&ObjID=".$this->obj->getId()."\"><img title=\"Edit\" src=\"". icons_url . "stock_edit.png\"/></div>";
		}
		// Delete
		if (hasPermission($_SESSION[sitename]["Username"], $_SESSION[sitename]["Permisos"] , "Delete", get_class($this->obj), "")) {
			$ret .= "\n<div class=\"operation\"><a href=\"javascript: ask('delete', '".get_class($this->obj) ." ". $this->obj->indexValues() . "', '".$this->obj->formphp."?Action=Delete&ObjID=".$this->obj->getID()."&ObjType=".get_class($this->obj)."&Delete".$this->obj->getId()."=yes');\"><img title=\"Delete\" src=\"". icons_url . "stock_delete.png\"/></a></div>";
		}
		$ret .= "\n</div>";		
		return $ret;
	}


	function listFormField($field) {
		if ($field->isIndex) {return $this->listObjectField($field);}
	}
	function tdinitField($field) {
		return "\n                  <div id=\"".$field->frmName($this) . "\">".
				"\n            <div class=\"field\">\n               <div class=\"".get_class($field->field)."\">\n               <div class=\"fieldname\">\n                  <h3>".$field->field->colName. "</h3>\n               </div>\n               <div class=\"fieldvalue\">";
	}
	function tdendField () {
		return "\n               </div>\n               </div>\n            </div>";
	}
	function listObjectField ($field) {
		return "\n                  </div>". 
				"\n         <div id=\"".$field->colName."listObject\">\n            <h3>".$this->makelink($field->viewValue(),"Edit",""). "</h3>\n         </div>";
	}
	function getWindowSize(){
		return array("heigth"=>300, "width"=>400);
	}
	function makeLinkAddress ($action, $append) {
		$link = new JSLinker;
		return $link->makelinkAddressPersistentObject($this->obj, $action, $append);
	}
	function dataType() {
		return get_class($this->obj);
	}
}

class PersistentCollectionHtmlDivEditView extends HtmlDivEditView  {
		function show () {
			$colec = $this->obj;
			$ret .= $this->showElements();
			$htmlobj = $this->viewFor(new $colec->dataType);
			$ret .= $htmlobj->makelink("Add", "Add", "");
			return $ret;
		}
      function showElements() {
         $colec = $this->obj;
         $obj = new $colec->dataType;
	 	 $html = $this->viewFor($obj);
		 $ret = "\n<div class=\"listColec\"> ";
         $ret .= $html->listHeader($this);
         $objs = $colec->objects();
		 $ret .= "\n   <div class=\"listObjectColec\"> ";
         if ($objs) {
         	foreach($objs as $index=>$object) {
	 	 		$html = $this->viewFor($object);
		 		$ret .= $html->listForm();
         	}
         }
		 $ret .= "\n   </div>\n</div>";
	  return $ret;
      }
      function asSelect ($name, $default) {
        $ret = "\n                  <select name=\"$name\">";
        $ret .= $this->voidOption ();
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlDivEditView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "&nbsp;\n                  </select>";
        return  $ret;
      }
      function userSelect ($name, $default) {
        $ret = "\n                  <select name=\"$name\" onSelect=javascript:autenticate(this) ;>";
        $ret .= $this->voidOption ();
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlDivEditView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "&nbsp;\n                  </select>";
        return  $ret;
      }
	function voidOption () {}
	function makeLinkAddress ($action, $append) {
		$link = new JSLinker;
		return $link->makelinkAddressPersistentCollection($this, $action, $append);
	}

	function getWindowSize(){
		return array("heigth"=>300, "width"=>400);
	}
	function dataType() {
		return get_class($this->obj)."&dataType=".$this->dataType;
	}
}
?>
