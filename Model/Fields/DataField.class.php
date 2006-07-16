<?

class DataField extends PWBObject {
	var $colName; // el nombre del campo
	var $value; // el valor almacenado en el campo
	var $isIndex; // Si se utiliza para identificarlo (por el usuario)
	var $owner; // The object the field belongs to
	var $displayString;
	var $buffered_value = null;
	var $modified = false;

	function DataField($name, $isIndex = false) {
		parent::PWBObject();
		$this->colName = $name;
		if (is_array($isIndex)) {
			if (isset($isIndex['is_index'])){
				$this->isIndex = $isIndex['is_index'];
			}
			if (isset($isIndex['display'])){
				$this->displayString = $isIndex['display'];
			}
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
		if ($operation=='SELECT'){
			return $this->owner->tableName().'.'.$this->colName
				   .' as '.$this->sqlName().	', ';
		} else {
			return $this->colName .	', ';
		}
	}
	function sqlName(){
		return $this->owner->tableName().'_'.$this->colName;
	}
	function SQLvalue() {}
	function insertValue() {
		return $this->SQLvalue();
	}
	function updateString() {
		return $this->colName . " = " . $this->SQLvalue();
	}
	function viewValue() {
		return $this->getValue();
	}
	function setValue($data) {
		$this->buffered_value =& $data;
		$this->modified = true;
		$this->triggerEvent('changed', $no_params = null);
	}

	function getValue() {
		if ($this->buffered_value !== null)
			return $this->buffered_value;
		else
			return $this->value;
	}

	function commitChanges() {
		$this->modified = false;
		$this->value =& $this->buffered_value;
		$this->triggerEvent('commited', $this);
	}

	function flushChanges() {
		$this->modified = false;
		$this->setValue($this->value);
		$this->triggerEvent('flushed', $this);
	}

	function isModified() {
		return $this->modified;
	}

	function loadFrom($reg) {
		$val = $reg[$this->sqlName()];
		$this->setValue($val);
	}

	function validate() {
		$this->triggerEvent('validated', $this);
		return false;
	}

	function requiredButEmpty() {
		$this->triggerEvent('required_but_empty', $this);
	}

	function canDelete() {
		return true;
	}

	function toArrayValue() {
		return $this->getValue();
	}

	function isEmpty() {
		return $this->getValue() == '';
	}

	function &copy() {
		/* Be aware that we don't copy the owner */
		$copy =& parent::copy();

		$copy->colName = $this->colName;
		$copy->value = $this->value;
		$copy->buffered_value = $this->buffered_value;
		$copy->isIndex = $this->isIndex;
		$copy->displayString = $this->displayString;

		return $copy;
	}
}

?>