<?

require_once(dirname(__FILE__)."/../ViewAction.class.php");

class EditAction extends ViewAction {
	function viewFor(&$obj){
		return $this->viewFactory($obj);
	}
	function show($obj, $linker, $showObjFields) {
/*		if ($obj->is_invalid_field($this->field->colName)) {
			$ret =  "<img title=\"Edit\" src=\"". icons_url . "stock_cancel.png\"/>";
		} else $ret ="";*/
		return $this->showField($this, $linker);
	}
	function frmName($renderer) {
		return $renderer->formName() . $this->field->colName;
	}
	function showListField ($renderer) {
		/*if ($this->field->isIndex) {*/return $this->showListFieldObject($renderer);
	}
	function showListFieldObject ($renderer) {
		return $this->field->viewValue();
	}	
}

/*

class EditActionTextArea extends EditAction {
	function formField ($object) {
		$ret .= "\n                     <textarea name=\"";
		$ret .= $this->frmName($object);
		$ret .= "\" >";
		$ret .= $this->field->convFromHTML($this->field->getValue());
		$ret .= "\n                     </textarea>";
		return $ret;
	}
}

class EditActionBoolField extends EditAction {      
	function listObject () {
		return "\n      ". $this->field->value;
	}
	function formField ($object) {
		if ($this->field->value){ $chk  =" CHECKED";} else { $chk  ="";}
		return "\n                     <input type=\"checkbox\" name=\"".$this->frmName($object)."\" $chk >";
	}
}


class EditActionDateTimeField	extends EditAction {
	function dateFormObject($object){
		$field = $this->field;
		$date = $field->dateArray();
		$ret = "";
		
		$ret .= "\n                     <div class=\"mday\"><div id=\"".$this->frmName($object) . "mday"."\">";
		
		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mday";
		$ret .= "\" value=\"";
		$ret .= $date["mday"];
		$ret .= "\"> / ";
		
		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"mon\"><div id=\"".$this->frmName($object) . "mon"."\">";		
		
		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "mon";
		$ret .= "\" value=\"";
		$ret .= $date["mon"];
		$ret .= "\"> /";
		
		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"year\"><div id=\"".$this->frmName($object) . "year"."\">";		

		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "year";
		$ret .= "\" value=\"";
		$ret .= $date["year"];
		$ret .= "\">";
		
		$ret .= "\n                     </div></div>";		
		return $ret;
	}
	function formField($object){
		return "\n            <div class=\"datefield\">".$this->dateFormObject($object) ."</div>\n            <div class=\"timefield\">". $this->timeFormObject($object)."\n                     </div>";
	}
	function timeFormObject($object) {
		$field = $this->field;
		$date = $field->dateArray();

		$ret = "\n                     <div class=\"hours\"><div id=\"".$this->frmName($object) . "hours"."\">";		
		
		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "hours";
		$ret .= "\" value=\"";
		$ret .= $date["hours"];
		$ret .= "\"> :";
		
		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"minutes\"><div id=\"".$this->frmName($object) . "minutes"."\">";		
	
		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "minutes";
		$ret .= "\" value=\"";
		$ret .= $date["minutes"];
		$ret .= "\"> :";
		
		$ret .= "\n                     </div></div>";
		$ret .= "\n                     <div class=\"seconds\"><div id=\"".$this->frmName($object) . "seconds"."\">";		
		
		$ret .= "\n                        <input type=\"text\" name=\"";
		$ret .= $this->frmName($object) . "seconds";
		$ret .= "\" value=\"";
		$ret .= $date["seconds"];
		$ret .= "\">";

		$ret .= "\n                     </div></div>";
		
		return $ret;
	}
}

class EditActionDateField			extends EditActionDateTimeField {
	function formField($object){
		return $this->dateFormObject($object);
	}
}
class EditActionTimeField			extends EditActionDateTimeField {
	function formField($object){
		return $this->timeFormObject($object);
	}
}
*/

?>
