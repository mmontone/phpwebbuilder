<?

require_once(dirname(__FILE__) . "/../Action/XMLNameFieldView.class.php");
require_once("HtmlFormEditView.class.php");

class XMLNameView extends HtmlFormEditView  {
	var $descentCount =-2;
	function fieldShowObjectFactory () {
		return new XMLNameFieldView;
	}
	function visitedPersistentCollection ($obj) {
		$view = new PersistentCollectionXMLNameView;
		$view->obj = $obj;
		return $view;
	}
	function visitedPersistentObject ($obj) {
		$view = new PersistentObjectXMLNameView;
		$view->obj = $obj;
		return $view;
	}
	function footers(){}
	function headers($form){
		if ($form["showXMLHeader"]=="TRUE") {
			/*header('Content-Type: application/xml');
			return "<?xml version=\"1.0\"?>";*/
		}

	}
}

class PersistentObjectXMLNameView extends XMLNameView  {
		function show(&$linker){
		header("Content-type: text/xml");
		return $this->showFields($linker, $this->obj->allFieldNames());
	}
	function showFields($linker, $fields) {
/*		$ret ="<?xml version=\"1.0\"?>"; */
		$this->descentCount = $_REQUEST["ObjectCount"];
		$ret = $this->showObject($linker, $fields);
		return $ret;
	}
	function showObject ($linker, $fields) {
		if ($this->descentCount > 0) {
			if($this->obj->getId()!=null and $this->obj->getId()>0){
		        $ret0 = "<" .$this->formName()." id=\"". $this->obj->getId() ."\" >";
				$ret = $this->fieldsForm($linker, $fields, TRUE);
		        $ret = $ret0 . $ret ."\n</".$this->formName().">";}
	         else {$ret="";}
		} else {$ret="";}
		return $ret;
	}
	function formName() {
		return $this->obj->table;
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
	function showField ($field, $linker) {
		$ret = $field->formObject($this, $linker);
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

class PersistentCollectionXMLNameView extends XMLNameView  {
	function show($linker=null) {
		$this->descentCount = $_REQUEST["ObjectCount"];
		$ret = $this->showColec(&$linker);
		return $ret;
	}
		function showColec ($linker=null) {
			$colec = $this->obj;
			$ret = $this->showElements($linker);
			return $ret;
		}
      function showElements($linker=ull) {
		if ($this->descentCount > 0) {
         $colec = $this->obj;
         $obj = new $colec->dataType;
	 	 $html = $this->viewFor($obj);
		 $ret0 = "<collection> ";
         $ret = $html->listHeader();
         $colec->limit=0;
         $objs = $colec->objects();
         if ($objs) {
         	foreach($objs as $index=>$object) {
	 	 		$html = $this->viewFor($object);
				$html->descentCount = ($this->descentCount);
		 		$ret .= $html->showObject($linker, $object->fieldNames);
         	}
         }
		 /*$ret = str_replace("\n", "\n   ", $ret);         */
		 $ret = $ret0 . $ret . "\n</collection>";
	  } else {$ret="";}
	  return $ret;
      }
      function asSelect ($name, $default) {
        $ret = "\n                  <index table=\"$name\" id=$default />";
        /*$ret .= $this->voidOption ();
        foreach ($this->obj->objects() as $index=>$obj) {
	    	$html = new XMLNameView;
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
