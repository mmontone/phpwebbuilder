<?

require_once(dirname(__FILE__) . "/../Action/XMLNameDTDFieldView.class.php");
require_once("HtmlFormEditView.class.php");

class XMLNameDTDView extends HtmlFormEditView  {
	function fieldShowObjectFactory () {
		return new XMLNameDTDFieldView;
	}
	function visitedPersistentCollection ($obj) {
		$view = new PersistentCollectionXMLNameDTDView;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject ($obj) {
		$view = new PersistentObjectXMLNameDTDView;
		$view->obj = $obj;
		return $view;
	}
}

class PersistentObjectXMLNameDTDView extends XMLNameDTDView  {
	function show () {
        $ret0 = "\n<" .$this->formName()." ID=". $this->obj->getId() .">";
		/*$ret .= $this->formHeader();
		$ret .= $this->formAppend();*/        	
		$ret .= $this->fieldsForm(TRUE);
		$ret = str_replace("\n", "\n   ", $ret);     
/*		$ret .= $this->formFooter();*/	
        $ret = $ret0 . $ret ."\n</".$this->formName().">";        	
		return $ret;
	}
	function formName() {
		return $this->obj->tableName();
	}	
	function listHeader () {
		return "";
	}
	function selForm ($default) {
		$this->obj->load();
		$ret = "\n                     <option value=\"".$this->obj->getID()."\"";
		if ($this->obj->getID() == $default) {$ret .= " selected";}
		$ret .= ">";
		$ret .= $this->obj->indexValues();
		$ret .= "\n                     </option>";
		return $ret;
	}
	function listForm () {
		/*$ret = "\n      <div class=\"listObjectLine\"> ";*/
		foreach($this->obj->allFields() as $index=>$field){
			if ($field->isIndex) {
				/*$ret .= "\n         <div class=\"listObjectCell\"> ";*/
				$ret .= $this->listFormField($field);
				/*$ret .= "\n         </div>";*/
			}
		}
		/*$ret .= "\n         <div class=\"BorrarlistObject\"> <A HREF=\"".$this->obj->formphp."?Action=Borrar&ObjType=".get_class($this->obj)."&ObjID=".$this->obj->id."&Borrar".$this->obj->id."=yes\"><H3>Borrar</H3></a>\n         </Div>";
		$ret .= "\n      </Div>";*/
		return $ret;
	}


	function listFormField($field) {
		if ($field->isIndex) {return $this->listObjectField($field);}
	}
	function tdinitField($field) {
		return "\n            <div class=\"field\">\n               <div class=\"".get_class($field->field)."\">\n               <div class=\"fieldname\">\n                  <h3>".$field->field->colName. "</h3>\n               </div>\n               <div class=\"fieldvalue\">";
	}
	function tdendField () {
		return "\n               </div>\n               </div>\n            </div>";
	}
	function listHeaderField($field) {
		return "\n      <div id=\"".$field->field->colName."listHeader\">\n         <h2>".$field->colName. "</h2>\n      </div>";
	}
	function listObjectField ($field) {
		$html = $this->fieldShowObjectFactory();
		$html = $html->viewFor($field);
		return $html->show($this, TRUE);
	}
	function showField ($field) {
		$ret = $field->formObject($this);
		return $ret;
	}
	function getWindowSize(){
		return array("heigth"=>300, "width"=>400);
	}
	function makeLinkAddress ($action, $append) {
		$link = new JSLinker;
		return $link->makelinkAddressPersistentObject($this, $action, $append);
	}
	function dataType() {
		return get_class($this->obj);
	}	
}

class PersistentCollectionXMLNameDTDView extends XMLNameDTDView  {
		function show () {
			$colec = $this->obj;
			$ret = $this->showElements();
			return $ret;
		}
      function showElements() {
         $colec = $this->obj;
         $obj = new $colec->dataType;
	 	 $html = $this->viewFor($obj);
		 $ret0 .= "\n<collection> ";	 	 
         $ret .= $html->listHeader();
         $objs = $colec->objects();
         if ($objs) {
         	foreach($objs as $index=>$object) {
	 	 		$html = $this->viewFor($object);
		 		$ret .= $html->show();
         	}
         }
		 $ret = str_replace("\n", "\n   ", $ret);         
		 $ret = $ret0 . $ret . "\n</collection>";
	  return $ret;	 
      }
      function asSelect ($name, $default) {
        $ret = "\n                  <index table=\"$name\" ID=$default />";
        /*$ret .= $this->voidOption ();
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new XMLNameDTDView;
    		$html = $html->viewFor($obj);
			$ret .= $html->selForm($default);
        }
        $ret .= "&nbsp;\n                  </select>";*/
        return  $ret;
      }
	function voidOption () {}
/*	function makeLinkAddress ($action, $append) {
		$link = new JSLinker;
		return $link->makelinkAddressPersistentCollection($this, $action, $append);
	}

	function getWindowSize(){
		return array("heigth"=>300, "width"=>400);
	}
	function dataType() {
		return get_class($this->obj)."&dataType=".$this->dataType;
	}*/
}
?>
