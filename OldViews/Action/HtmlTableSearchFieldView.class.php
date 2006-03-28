<?

class HtmlTableSearchFieldView extends AbstractFieldView {

	function show ($obj, $ShowObjFields, $field_name) {
	
		$ret = $this->tdinit();
		$ret .= "Filter by: <input type=\"checkbox\" value=\"yes\" name=\"filter". $field_name ."\"";
		if (isset($obj->conds[$this->frmName($obj)])) $ret .= "checked"; 				
		$ret .=	" />";
		$ret .= $this->formObject($obj);
		$ret .= $this->tdend();
		return $ret;
	}
	function searchQuery($form, $view) {
		return array($form["cond".$this->frmName($view)], $form["val".$this->frmName($view)]);
	}
	function frmName($html) {
		return $this->field->colName;
	}
	function listForm ($object) {
		if ($this->field->isIndex) {return $this->listObject($object);}
	}
	function visitedTextField($field) {
		$view = new HtmlTableSearchTextFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateTimeField($field) {
		$view = new HtmlTableSearchDateTimeFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedDateField($field) {
		$view = new HtmlTableSearchDateFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTimeField($field) {
		$view = new HtmlTableSearchTimeFieldView;
		$view->field = $field;
		return $view;
	}

	function formObject ($object) {}
	function visitedCollectionField($field) {
		$view = new HtmlTableSearchCollectionFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedTextArea($field) {
		$view = new HtmlTableSearchTextAreaView;
		$view->field = $field;
		return $view;
	}
	function visitedBoolField($field) {
		$view = new HtmlTableSearchBoolFieldView	;
		$view->field = $field;
		return $view;
	}
	function visitedNumField($field) {
		$view = new HtmlTableSearchNumFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedPasswordField($field) {
		$view = new HtmlTableSearchPasswordFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedEmailField($field) {
		$view = new HtmlTableSearchEmailFieldView;
		$view->field = $field;
		return $view;
	}	
	function visitedIdField($field) {
		$view = new HtmlTableSearchIdFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedIndexField($field) {
		$view = new HtmlTableSearchIndexFieldView;
		$view->field = $field;
		return $view;
	}
	function visitedUserField($field) {
		$view = new HtmlTableSearchUserFieldView;
		$view->field = $field;
		return $view;
	}
	function tdinit() {
		return "\n<tr><td width=\"16%\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\"> ".$this->field->colName."</font></td>
		<td width=\"84%\"> <font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">";
	}
	function tdend () {
		return "\n</font></td></tr>";
	}
	function listHeader($colview) {
               $ret = "\n<td align=center>" .
                               "<a href=\"". $colview->makeLinkAddress("Show", "&order=%20ORDER%20BY%20".$this->field->colName) ."\">". //
                               "<b>".$this->field->colName."</b>" .
                               "</a></td>";
               return $ret;
	}	
	function listObject ($object) {
		$html = new HtmlTableSearchView;
		$html = $html->viewFor($object);

//		return "\n<td>".$html->makelink($this->field->viewValue())."</td>";
		return "\n<td>".$this->field->viewValue()."</td>";

	}
}

class HtmlTableSearchCollectionFieldView extends HtmlTableSearchFieldView {
	function formObject ($object) {
		$html = new HtmlTableSearchView;
		$colec = $this->field->collection;
		$html = $html->viewFor($colec);
		$ret = $html->showElements();
		$ret .= "\n<a href=\"$colec->formphp?Action=Add&". $colec->dataType .$this->field->fieldname ."=". $this->field->value ."&ObjType=$colec->dataType\">";	 

		$ret .= "\n<img title=\"New\" src=\"". icons_url . "stock_new-16.png\"/></A>";

		return $ret;
	}

	function show ($object, $objFields, $field_name) {

		if ($objFields) {return parent::show ($object, $objFields, $field_name);}

	}
}

class HtmlTableSearchIndexFieldView extends HtmlTableSearchFieldView {
	function formObject ($object) {
		$html = new HtmlTableSearchView;
		$html = $html->viewFor($this->field->collection);
		$ret = "\n<input type=\"hidden\" name=\"cond";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"=\">";
		return $ret.$html->asSelect("val".$this->frmName($object), $object->conds[$this->frmName($object)][1], $this->field->nullValue);
	}

	/*function show ($object, $objFields, $field_name) {

		if ($objFields) {return parent::show ($object, $objFields, $field_name);}

	}*/
}
class HtmlTableSearchUserFieldView extends HtmlTableSearchIndexFieldView {
	function formObject ($object) {
		$html = new HtmlTableSearchView;
		$html = $html->viewFor($this->field->collection);
		$ret = "\n<input type=\"hidden\" name=\"cond";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"=\">";
		return $ret.$html->userSelect("val".$this->frmName($object), $object->conds[$this->frmName($object)][1], $this->field->nullValue);
	}

}

class HtmlTableSearchDateFieldView extends HtmlTableSearchDateTimeFieldView {
	function formObject($object){
		return $this->dateFormObject($object);
	}
}
class HtmlTableSearchTimeFieldView extends HtmlTableSearchDateTimeFieldView {
	function formObject($object){
		return $this->timeFormObject($object);
	}
}
class HtmlTableSearchDateTimeFieldView extends HtmlTableSearchFieldView {
	function dateFormObject($object){
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mday";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["mday"];
		$ret .= "\"> / ";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mon";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["mon"];
		$ret .= "\"> /";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "year";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["year"];
		$ret .= "\"> &nbsp&nbsp&nbsp&nbsp ";
		return $ret;
	}
	function formObject($object){
		return $this->dateFormObject($object) . $this->timeFormObject($object);
	}
	function timeFormObject($object) {
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "hours";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["hours"];
		$ret .= "\"> :";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "minutes";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["minutes"];
		$ret .= "\"> :";
		$ret .= "\n<input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "seconds";
		$ret .= "\" size=\"5\" value=\"";
		$ret .= $date["seconds"];
		$ret .= "\">";
		return $ret;
	}
}

class HtmlTableSearchNumFieldView extends HtmlTableSearchFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"hidden\" name=\"cond";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\" like \">";
		$ret .= "\n<input type=\"text\" name=\"val";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"";
		$ret .= $this->field->getValue();
		$ret .= "\">";
		return $ret;
	}
}

class HtmlTableSearchTextFieldView extends HtmlTableSearchFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"hidden\" name=\"cond";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\" like \">";
		$ret .= "\n<input type=\"text\" name=\"val";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\"";
		$ret .= ereg_replace("[\'%]", "", $object->conds[$this->frmName($object)][1]);
		$ret .= "\">";
		return $ret;
	}
	function searchQuery($form, $view) {
		return array($form["cond".$this->frmName($view)], "'%".$form["val".$this->frmName($view)]."%'");
	}
	
}

class HtmlTableSearchPasswordFieldView extends HtmlTableSearchTextFieldView {
	function formObject($object) {
		$ret = "\n<input type=\"password\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "1\" size=\"60\" value=\"";
		//$ret .= $this->field->getValue();
		$ret .= "\">";
		$ret .= "\n<input type=\"password\" name=\"";
		$ret .= $this->frmName($object);
		$ret .= "2\" size=\"60\" value=\"";
		//$ret .= $this->field->getValue();
		$ret .= "\">";
		return $ret;
	}

}
class HtmlTableSearchEmailFieldView extends HtmlTableSearchTextFieldView {}

class HtmlTableSearchIdFieldView extends HtmlTableSearchFieldView {
	function formObject ($object) {
		return "\n<td align=\"center\">".$this->field->value."</td>";
	}
	function listObject () {
		return "\n<td>".$this->field->value."</td>";
	}
	function show(){
		return "";
	}
}

class HtmlTableSearchTextAreaView extends HtmlTableSearchFieldView {
	function listObject () {
		return "\n<td>". $this->field->getValue()."</td>";
	}
	function formObject ($object) {
		$ret = "\n<input type=\"hidden\" name=\"cond";
		$ret .= $this->frmName($object);
		$ret .= "\" size=\"60\" value=\" like \">";
		$ret .= "\n<textarea name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" cols=\"50\" rows=\"15\">";
		$ret .= $object->conds[$this->frmName($object)][1];
		$ret .= "\n</textarea>";
		return $ret;
	}
}

class HtmlTableSearchBoolFieldView extends HtmlTableSearchFieldView {      
	function listObject () {
		return "\n<td>". $this->field->value."</td>";
	}
	function formObject ($object) {
		if ($this->field->getValue()){ $chk  =" CHECKED";} else { $chk  ="";}
		return "<input type=\"checkbox\" name=\"".$this->frmName($object)."\" $chk >";
	}
	function readForm ($object, $form) {
		$name = $this->frmName($object);
		if (isset($form[$name]) AND ($form[$name]=="on")) {$this->field->value=1; } else {$this->field->value=0; };
	}
}

?>
