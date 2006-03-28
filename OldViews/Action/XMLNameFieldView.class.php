<?

class XMLNameFieldView extends AbstractFieldView {
	function show($obj, $linker, $showObjFields) {
		return $obj->showField($this, $linker);
	}
	function frmName($object) {
		return $this->field->colName;
	}
	function visitedTextField($field) {
		$view = new XMLNameTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmailField($field) {
		$view = new XMLNameTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateTimeField($field) {
		$view = new XMLNameDateTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateField($field) {
		$view = new XMLNameDateFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTimeField($field) {
		$view = new XMLNameTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedCollectionField($field) {
		$view = new XMLNameCollectionFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTextArea($field) {
		$view = new XMLNameTextAreaView;
		$view->field = $field;
		return $view;
	}
	function visitedBoolField($field) {
		$view = new XMLNameBoolFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedPasswordField($field) {
		$view = new XMLNamePasswordFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedNumField($field) {
		$view = new XMLNameNumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIdField($field) {
		$view = new XMLNameIdFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIndexField($field) {
		$view = new XMLNameIndexFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedUserField($field) {
		$view = new XMLNameUserFieldView;
		$view->field = $field;
		return $view;
	}
}


class XMLNameCollectionFieldView extends XMLNameFieldView {
	function formObject ($object, $linker) {
		$colec = $this->field->collection;
		$html = $object->viewFor($colec);
		$html->descentCount = $object->descentCount-1;
		$ret = $html->showColec($linker);
		return $ret;
	}
	function show ($object, $linker, $objFields) {
		if ($objFields) {return parent::show ($object, $linker, $objFields);}
	}
}

class XMLNameIndexFieldView extends XMLNameFieldView {
	function formObject ($object, $linker) {
		$html = $object->viewFor($this->field->collection);
		return $html->asSelect($this->frmName($object), $this->field->getValue());
	}
	function show ($object, $linker, $objFields) {
		/*if ($objFields) {*/
		/* necesito obtener el objeto apuntado, y luego mostrarlo. */
		$obj = new $this->field->collection->dataType;
		/*$ret .= $this->field->collection->dataType;
		$ret .= ($this->field->value);*/
		
		$obj->setId($this->field->value);
		$obj->load();
		/*$ret .= print_r($obj, TRUE);*/
		 
		$html = new XMLNameView;	
		$html = $html->viewFor($obj);
		$html->descentCount = $object->descentCount-1;
		$ret .= $html->showObject($linker, $obj->fieldNames);
		return $ret;
//}
	}
}
class XMLNameUserFieldView extends XMLNameIndexFieldView {
	function formObject ($object, $linker) {
		$html = $object->viewFor($this->field->collection);
		if(!(isset($_SESSION[sitename]["id"])))
		//ISERTAR QUE MUESTRE LOGIN
			$ret=$html->asSelect($this->frmName($object), $this->field->getValue());
		else $ret="";
		return $ret;
	}
}

class XMLNameNumFieldView extends XMLNameFieldView {
	function formObject($object) {
		$ret .= "\n<";
		$ret .= $this->frmName($object);
		$ret .= " type=\"num\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}
}

class XMLNameTextFieldView extends XMLNameFieldView {
	function formObject($object) {
		$ret = "\n<";
		$ret .= $this->frmName($object);
		$ret .= " type=\"text\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\" />";
		return $ret;
	}
}

class XMLNamePasswordFieldView extends XMLNameTextFieldView {}

class XMLNameIdFieldView extends XMLNameFieldView {
	function formObject ($object) {
		return ""; //\n<ID VALUE=\"".$this->field->value."\" />";
	}
}

class XMLNameTextAreaView extends XMLNameFieldView {
	function formObject ($object) {
		$ret = "\n<";
		$ret .= $this->frmName($object);
		$ret .= " type=\"text-area\" >";
               $text = $this->field->convFromHTML($this->field->getValue());
               $text = ereg_replace("\n\n","\n", $text);
               $text = ereg_replace("\n",chr(10), $text);
               $text = ereg_replace(chr(13).chr(10),chr(10), $text);
		$ret .= $text;
		$ret .= "</".$this->frmName($object).">";
		return $ret;
	}
}

class XMLNameBoolFieldView extends XMLNameFieldView {      
	function listObject () {
		return "\n". $this->field->value;
	}
	function formObject ($object) {
		if ($this->field->value){ $chk  ="TRUE";} else { $chk  ="FALSE";}
		return "\n<".$this->frmName($object)." type=\"bool\" value=\"$chk\" />";
	}
}

class XMLNameDateFieldView			extends XMLNameDateTimeFieldView {
	function formObject($object){
		return "\n<". $this->frmName($object) ." type=\"date\">".$this->dateFormObject($object) ."\n                     </".$this->frmName($object).">";
	}
}
class XMLNameTimeFieldView			extends XMLNameDateTimeFieldView {
	function formObject($object){
		return "\n<". $this->frmName($object) ." type=\"time\">" . $this->timeFormObject($object)."\n                    </".$this->frmName($object).">";
	}
}
class XMLNameDateTimeFieldView	extends XMLNameFieldView {
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
		return "\n<". $this->frmName($object) ." type=\"datetime\">".$this->dateFormObject($object) . $this->timeFormObject($object)."\n                     </".$this->frmName($object).">";
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


?>
