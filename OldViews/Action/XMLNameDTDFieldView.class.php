<?

require_once("AbstractFieldView.class.php");

class XMLNameDTDFieldView extends AbstractFieldView {
	function show($obj, $showObjFields) {
		return $obj->showField($this);
	}
	function frmName($object) {
		return $this->field->colName;
	}
	function visitedTextField($field) {
		$view = new XMLNameDTDTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateTimeField($field) {
		$view = new XMLNameDTDDateTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateField($field) {
		$view = new XMLNameDTDDateFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTimeField($field) {
		$view = new XMLNameDTDTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedCollectionField($field) {
		$view = new XMLNameDTDCollectionFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTextArea($field) {
		$view = new XMLNameDTDTextAreaView;
		$view->field = $field;
		return $view;
	}
	function visitedBoolField($field) {
		$view = new XMLNameDTDBoolFieldView	;
		$view->field = $field;
		return $view;
	}
	function visitedNumField($field) {
		$view = new XMLNameDTDNumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIdField($field) {
		$view = new XMLNameDTDIdFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIndexField($field) {
		$view = new XMLNameDTDIndexFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedUserField($field) {
		$view = new XMLNameUserFieldView;
		$view->field = $field;
		return $view;
	}
}


class XMLNameDTDCollectionFieldView extends XMLNameDTDFieldView {
	function formObject ($object) {
/*		$colec = $this->field->collection;
		$html = $object->viewFor($colec);
		$ret = $html->showElements();
		$htmlobj = $object->viewFor(new $colec->dataType);
		$ret .= $htmlobj->makelink("Agregar", "Add", "&".$colec->dataType.$this->field->fieldname."=".$object->obj->getID());*/
		return "";
	}
	function show ($object, $objFields) {
		if ($objFields) {return parent::show ($object, $objFields);}
	}
}

class XMLNameDTDIndexFieldView extends XMLNameDTDFieldView {
	function formObject ($object) {
		$html = $object->viewFor($this->field->collection);
		return $html->asSelect($this->frmName($object), $this->field->getValue());
	}
	function show ($object, $objFields) {
		/*if ($objFields) {*/
		/* necesito obtener el objeto apuntado, y luego mostrarlo. */
		$obj = new $this->field->collection->dataType;
		/*$ret .= $this->field->collection->dataType;
		$ret .= ($this->field->value);*/
		
		$obj->setId($this->field->value);
		$obj->load();
		/*$ret .= print_r($obj, TRUE);*/
		 
		$html = new XMLNameDTDView;	
		$html = $html->viewFor($obj);
		$ret .= $html->show();
		return $ret;
//}
	}
}

class XMLNameDTDUserFieldView extends XMLNameDTDFieldView {
	function formObject ($object) {
		$html = $object->viewFor($this->field->collection);
		if(!(isset($_SESSION[sitename]["id"])))
		//ISERTAR QUE MUESTRE LOGIN
			$ret=$html->asSelect($this->frmName($object), $this->field->getValue());
		else $ret="";
		return $ret;
	}
}

class XMLNameDTDNumFieldView extends XMLNameDTDFieldView {
	function formObject($object) {
		$ret .= "\n<";
		$ret .= $this->frmName($object);
		$ret .= " type=\"NUM\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}
}

class XMLNameDTDTextFieldView extends XMLNameDTDFieldView {
	function formObject($object) {
		$ret .= "\n<";
		$ret .= $this->frmName($object);
		$ret .= " type=\"TEXT\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}
}

class XMLNameDTDIdFieldView extends XMLNameDTDFieldView {
	function formObject ($object) {
		return ""; //\n<id value=".$this->field->value." />";
	}
}

class XMLNameDTDTextAreaView extends XMLNameDTDFieldView {
	function formObject ($object) {
		$ret .= "\n<";
		$ret .= $this->frmName($object);
		$ret .= " type=\"text-area\" >";
		$ret .= $this->field->convFromHTML($this->field->getValue());
		$ret .= "\n</".$this->frmName($object).">";
		return $ret;
	}
}

class XMLNameDTDBoolFieldView extends XMLNameDTDFieldView {      
	function listObject () {
		return "\n". $this->field->value;
	}
	function formObject ($object) {
		if ($this->field->value){ $chk  ="TRUE";} else { $chk  ="FALSE";}
		return "\n<".$this->frmName($object)." type=\"BOOL\" value=$chk />";
	}
}

class XMLNameDTDDateTimeFieldView	extends XMLNameDTDFieldView {
	function dateFormObject($object){
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "";
		
		$ret .= "\n   <mday name=\"";
		$ret .= $this->frmName($object) . "mday";
		$ret .= "\" value=\"";
		$ret .= $date["mday"];
		$ret .= "\" />";
		
		$ret .= "\n   <mon name=\"";
		$ret .= $this->frmName($object) . "mon";
		$ret .= "\" value=\"";
		$ret .= $date["mon"];
		$ret .= "\" />";
		
		$ret .= "\n   <year name=\"";
		$ret .= $this->frmName($object) . "year";
		$ret .= "\" value=\"";
		$ret .= $date["year"];
		$ret .= "\" />";
		
		return $ret;
	}
	function formObject($object){
		return "\n<". $this->frmName($object) ." type=\"DATETIME\">".$this->dateFormObject($object) . $this->timeFormObject($object)."\n                     </".$this->frmName($object).">";
	}
	function timeFormObject($object) {
		$field = $this->field;
		$date = $field->dateArray();
		$ret .= "\n   <hour name=\"";
		$ret .= $this->frmName($object) . "hours";
		$ret .= "\" value=\"";
		$ret .= $date["hours"];
		$ret .= "\" />";
		
		$ret .= "\n   <min name=\"";
		$ret .= $this->frmName($object) . "minutes";
		$ret .= "\" value=\"";
		$ret .= $date["minutes"];
		$ret .= "\" />";
		
		$ret .= "\n   <sec name=\"";
		$ret .= $this->frmName($object) . "seconds";
		$ret .= "\" value=\"";
		$ret .= $date["seconds"];
		$ret .= "\" />";

		return $ret;
	}
}

class XMLNameDTDDateFieldView			extends XMLNameDTDDateTimeFieldView {
	function formObject($object){
		return "\n<". $this->frmName($object) ." type=\"DATE\">".$this->dateFormObject($object) ."\n                     </".$this->frmName($object).">";
	}
}
class XMLNameDTDTimeFieldView			extends XMLNameDTDDateTimeFieldView {
	function formObject($object){
		return "\n<". $this->frmName($object) ." type=\"TIME\">" . $this->timeFormObject($object)."\n                    </".$this->frmName($object).">";
	}
}


?>
