<?

class FieldMapper extends PWBFactory{
	var $field;

	function &createInstanceFor(&$field){
		$this->field =& $field;
		return $this;
	}

	function findModifications(&$obj) {
		return $obj->findModifications($this);
	}
	function creation(&$obj) {
		return "\n   `".$this->field->colName ."` ". $this->allType().", ";
	}
	function compareType ($t) {
		return eregi($this->typeEreg(), $t);
	}
	function typeEreg(){
		return "^".$this->type()."$";
	}
	function unique(){}
	function createUnique(){
		return  "`".$this->field->colName."`".$this->unique();
	}
	function getDefault(){
		return $this->field->creationParams['default'];
	}
	function checkField($f){
		return $this->compareType($f["Type"]) &&
				(($f["Default"]===null && ($f["Null"]==='YES' || $f["Extra"]=='auto_increment') && $this->getDefault()===null)
				|| ($f["Default"]==$this->getDefault() && $f["Default"]!==null
					&& $f["Null"]==='NO'));
	}
	function allType(){
		$df = $this->getDefault();
		$type = $this->type();
		if ($df==null){
			return $type;
		} else {
			return $type.' NOT NULL DEFAULT '.$df;
		}
	}
}

class CollectionFieldFieldMapper extends FieldMapper {
	function creation (&$object) {
		return "";
	}
	function findModifications (&$object) {
		return "";
	}
	function createUnique($i){return '';}
}
class TableCheckEnumFieldView extends FieldMapper {
	function type (){
		return "TEXT";
	}
}
class EmbedFieldFieldMapper extends FieldMapper {}

class NumFieldFieldMapper extends FieldMapper {
	function type (){
		$nt = @$this->field->creationParams['numtype'];
		if ($nt != '' && $nt != 'int') {
			return "float";
		} else {
			return "int(11)";
		}
	}
	function typeEreg(){
		$nt = @$this->field->creationParams['numtype'];
		if ($nt != '' && $nt != 'int') {
			return "float";
		} else {
			return "^int\([0-9]*\)";
		}
	}
}

class IndexFieldFieldMapper extends NumFieldFieldMapper {}

class TextFieldFieldMapper extends FieldMapper {
	function unique(){
		return "(50)";
	}
	function type (){
		return "TEXT";
	}
}

class PasswordFieldFieldMapper extends TextFieldFieldMapper {
	function unique(){}
	function type (){
		return "TEXT";
	}
}


class EmailFieldFieldMapper extends TextFieldFieldMapper {}

class IdFieldFieldMapper extends NumFieldFieldMapper {
	function type(){
		return "int(11) unsigned NOT NULL AUTO_INCREMENT";
	}
}

class VersionFieldFieldMapper extends NumFieldFieldMapper {
	function type(){
		return "int(11) unsigned";
	}
	function getDefault(){
		return '0';
	}
}

class SuperFieldFieldMapper extends NumFieldFieldMapper {
	function type(){
		return "int(11) unsigned UNIQUE";
	}
	function getDefault(){
		return 0;
	}
}

class FilenameFieldFieldMapper extends TextFieldFieldMapper {}
class BlobFieldFieldMapper extends FieldMapper {
	function type (){
		return "LONGBLOB";
	}
}
class TextAreaFieldMapper extends TextFieldFieldMapper {
	function type (){
		return "LONGTEXT";
	}
}

class BoolFieldFieldMapper extends NumFieldFieldMapper {
	function type (){
		return "int(1)";
	}
}

class DateTimeFieldFieldMapper		extends FieldMapper {
	function type (){
		return "datetime";
	}
}

class DateFieldFieldMapper			extends DateTimeFieldFieldMapper {
	function type (){
		return "date";
	}
}

class TimeFieldFieldMapper			extends DateTimeFieldFieldMapper {
	function type (){
		return "time";
	}
}
