<?

require_once "HtmlTable.class.php";

class HtmlTablePersistentCollection extends HtmlTable {
      function show ($linker) {
	 	 $ret .= $this->showElements($linker);
	 	 $ret.=$this->showLinks($linker);
         return $ret;
      }
	function dataHeader($renderer){
         $colec = $this->obj;

	 	 $ret = "\n<table><tbody>";

         $obj = new $colec->dataType;
		 $html = $this->viewFor($obj);
         //$ret .= $html->listHeader($renderer);
		 return $ret;
	}
	function dataFooter($renderer){
         $ret .= "\n</tbody></table>";
		 $ret .= $this->showLinks($renderer);
         return $ret;         
	}	      
    function dataFields($renderer) {
        $objs = $this->obj->objects();
    	foreach($objs as $index=>$object) {
     		$ret .= $renderer->showListObject($object);
     	}
        return $ret;
    }
	function objectWrapperInit(){
		return "<tr>";
	}
	function objectWrapperEnd(){
		return "</tr>";
	}
	function fieldWrapperInit(){
		return "<td>";
	}
	function fieldWrapperEnd(){
		return "</td>";
	}
    function showLinks($renderer) {
    	$colec = $this->obj;
		 if (fHasAnyPermission($_SESSION[sitename]["id"], $this->obj->dataType,"Add")) {
		    $ret .= $renderer->linker->colecAddLink($colec->dataType, $colec->formphp);
		 }
		  if (($this->obj->offset-$this->obj->limit) < 0) {
		  	$back_offset=0;
		  } else {
		  	$back_offset = $this->obj->offset-$this->obj->limit;
		  }
		  $ret .= $renderer->linker->showColecBack($this, array("offset"=>$back_offset));
		  $ret .= $renderer->linker->showColecNext($this, array("offset"=>($this->obj->offset+$this->obj->limit)));
		  $ret .= $renderer->linker->showObjSearch($this, $this->obj->dataType);

         return $ret;
      }
      function showLinksField($object, $field, $linker) {
      	$colec = $this->obj;
		 if (fHasAnyPermission($_SESSION[sitename]["id"], $this->obj->dataType,"Add")) {
		    $ret .= $linker->showObjAdd($colec->formphp, $colec->dataType, $colec->dataType.$field->field->fieldname ."=". $field->field->value);
		 }
		  if (($this->obj->offset-$this->obj->limit) < 0) {
		  	$back_offset=0;
		  } else {
		  	$back_offset = $this->obj->offset-$this->obj->limit;
		  }
		  $ret .= $linker->showColecBack($this, array("offset"=>$back_offset));
		  $ret .= $linker->showColecNext($this, array("offset"=>($this->obj->offset+$this->obj->limit)));
		  $ret .= $linker->showObjSearch($this, $this->obj->dataType);

         return $ret;
      }
      function asSelect ($linker, $name, $default, $void) {
        $ret = "\n<select name=\"$name\">";
        if ($void!=""){
        	$ret .= "<option value=\"0\">$void</option>";
        }
        $this->obj->limit=5000;
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlTableEditView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "</select>";
        $obj = new $this->obj->dataType;
        $ret .= $linker->showSelectAdd($obj->formphp, get_class($obj));
        return  $ret;
      }
      function userSelect ($linker, $name, $default, $void) {
        $ret = "\n<select name=\"$name\" onSelect=javascript:autenticate(this) ;>";
        if ($void!=""){
        	$ret .= "<option value=\"0\">$void</option>";
        }
        $this->obj->limit=5000;
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new HtmlTableEditView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "</select>";
        $obj = new $this->obj->dataType;
        $ret .= $linker->showSelectAdd($obj->formphp, get_class($obj));
        return  $ret;
      }
      
      function voidOption () {}
	function makeLinkAddress ($action, $append) {
		$link = new PlainLinker;
		return $link->makelinkAddressPersistentCollection($this, $action, $append);
	}

}
?>
