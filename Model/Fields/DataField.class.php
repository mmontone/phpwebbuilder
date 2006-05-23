<?
require_once dirname(__FILE__) . '/../../newcontroller/PWBObject.class.php';

class DataField extends PWBObject {
	var $colName; // el nombre del campo
	var $value; // el valor almacenado en el campo
	var $isIndex; // Si se utiliza para identificarlo (por el usuario)
	var $owner; // The object the field belongs to
	var $displayString;

	function DataField($name, $isIndex = false) {
		$this->colName = $name;
		if (is_array($isIndex)) {
			$this->isIndex = $isIndex['is_index'];
			$this->displayString = $isIndex['display'];
		}
		else {
			$this->isIndex = $isIndex;
		}
		if (!$this->displayString)
			$this->displayString = ucfirst($name);
	}

	function renderAction($action) {
		$this->owner->renderAction($action);
	}

	/*function ConvHTML($s) {
	    return mb_convert_encoding($s,"HTML-ENTITIES","auto");
	}*/

	function ConvHTML2($s) {
		$s = str_replace("ï¿½", "&aacute;", $s);
		$s = str_replace("ï¿½", "&eacute;", $s);
		$s = str_replace("ï¿½", "&iacute;", $s);
		$s = str_replace("ï¿½", "&oacute;", $s);
		$s = str_replace("ï¿½", "&uacute;", $s);
		$s = str_replace("ï¿½", "&Aacute;", $s);
		$s = str_replace("ï¿½", "&Eacute;", $s);
		$s = str_replace("ï¿½", "&Iacute;", $s);
		$s = str_replace("ï¿½", "&Oacute;", $s);
		$s = str_replace("ï¿½", "&Uacute;", $s);
		/*  $s = str_replace ("\"", "\\\"", $s);*/
		$s = str_replace(chr(13) . chr(10) . chr(13) . chr(10), "<p>", $s);
		$s = str_replace(chr(09), "&nbsp;&nbsp;&nbsp;&nbsp;", $s);
		$s = str_replace("    ", "&nbsp;&nbsp;&nbsp;&nbsp;", $s);
		//  $s = str_replace (" ", "&nbsp;", $s);
		/*  $s = ereg_replace("(\n| )*$", "", $s);*/
		return $s;
	}
	function trim($s) {
		/*$s = ereg_replace("(\\n| )*$", " ", $s);*/
		return $s;
	}

	function convFromHTML($s) {
		$s = str_replace("ï¿½", "&aacute;", $s);
		$s = str_replace("&eacute;", "ï¿½", $s);
		$s = str_replace("&iacute;", "ï¿½", $s);
		$s = str_replace("&oacute;", "ï¿½", $s);
		$s = str_replace("&uacute;", "ï¿½", $s);
		$s = str_replace("&Aacute;", "ï¿½", $s);
		$s = str_replace("&Eacute;", "ï¿½", $s);
		$s = str_replace("&Iacute;", "ï¿½", $s);
		$s = str_replace("&Oacute;", "ï¿½", $s);
		$s = str_replace("&Uacute;", "ï¿½", $s);
		$s = str_replace("\\\"", "\"", $s);
		$s = str_replace("\\'", "'", $s);
		$s = ereg_replace("(<br>| )*$", "", $s);
		$s = str_replace("<br>", chr(13) . chr(13), $s);
		$s = str_replace("<p>", chr(13) . chr(10) . chr(13) . chr(10), $s);
		$s = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", "    ", $s);
		return $s;
	}
	function & visit(& $obj) {
		return $obj->visitedDataField($this);
	}
	function setID($id) {}
	function fieldName($operation) {
		return $this->owner->tableName().'.'.$this->colName . ', ';
	}

	function SQLvalue() {}
	function insertValue() {
		return $this->SQLvalue();
	}
	function updateString() {
		return $this->colName . " = " . $this->SQLvalue();
	}
	function viewValue() {
		return $this->value;
	}
	function setValue($data) {
		$this->value = $data;
		$this->triggerEvent('changed', $no_params = null);
	}
	function getValue() {
		return $this->value;
	}
	function loadFrom($reg) {
		$val = $reg[$this->colName];
		$this->setValue($val);
		return $this->check($val);
	}
	function check($val) {
		return TRUE;
	}
	function validate($val, & $errors) {
		return true;
	}
	function canDelete() {
		return true;
	}
	function toArrayValue() {
		return $this->value;
	}
}

?>